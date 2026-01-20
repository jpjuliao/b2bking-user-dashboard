<?php

namespace JPJULIAO\B2BKing_Addons;

class Shop_Filters_Renderer extends Shop_Filters_Base
{

  public function __construct()
  {
    add_shortcode(
      'shop_filter_container',
      [$this, 'render_container_shortcode']
    );
    add_shortcode(
      'shop_filter_section',
      [$this, 'render_section_shortcode']
    );
  }

  public function render_container_shortcode(array $atts, string $content): string
  {
    $shop_url = get_permalink(wc_get_page_id('shop'));
    $content = $this->remove_unwanted_shortcodes(
      $content,
      ['shop_filter_section']
    );
    ob_start();
    ?>
    <form method="get" action="<?php echo esc_url($shop_url); ?>" class="shop-filters-form">
      <?php
      foreach ($_GET as $key => $value) {
        if (in_array($key, ['filter', 'product_cat', 'product_tag', 'product_brand'])) {
          continue;
        }
        if (strpos($key, 'filter_') === 0) {
          continue;
        }

        if (is_array($value)) {
          foreach ($value as $v) {
            echo '<input type="hidden" name="' . esc_attr($key) . '[]" value="' . esc_attr($v) . '" />';
          }
        } else {
          echo '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '" />';
        }
      }
      echo do_shortcode($content);
      ?>
      <div class="shop-filters-control">
        <button type="submit" class="apply-filters-btn">Apply Filters</button>
      </div>
    </form>
    <?php
    return ob_get_clean();
  }

  public function render_section_shortcode(array $atts, string $content): string
  {
    $section = $atts['section'];

    if ($section === 'best-new-discounts') {
      return $this->render_product_best_new_discounts_filter($atts);
    }
    if (in_array($section, ['product_tag', 'product_cat', 'product_brand'])) {
      return $this->render_product_taxonomies_filter($atts, $section);
    }
    if ($section === 'attributes') {
      return $this->render_product_attributes_filter($atts);
    }

    return '';
  }

  public function render_product_attributes_filter(array $atts): string
  {
    $settings = $this->get_filter_setting('attributes');
    if (!$settings['enabled']) {
      return '';
    }

    if (!function_exists('wc_get_attribute_taxonomies')) {
      return '';
    }

    $output = '';
    $attribute_taxonomies = wc_get_attribute_taxonomies();

    foreach ($attribute_taxonomies as $attribute) {
      $taxonomy = wc_attribute_taxonomy_name($attribute->attribute_name);

      if (!taxonomy_exists($taxonomy)) {
        continue;
      }

      $options = $this->get_terms_options($taxonomy);

      if (empty($options)) {
        continue;
      }

      $param_name = 'filter_' . $attribute->attribute_name;
      $current = $this->get_filter_values($param_name);

      $output .= $this->render_checkbox_list(
        $attribute->attribute_label,
        $options,
        $param_name,
        $current
      );
    }

    return $output;
  }

  public function render_product_best_new_discounts_filter(array $atts): string
  {
    $current_filters = $this->get_filter_values('filter');

    $filters = [];

    $best_settings = $this->get_filter_setting('best_sellers');
    if ($best_settings['enabled']) {
      $filters['best'] = $best_settings['title'] ?: 'Best Sellers';
    }

    $new_settings = $this->get_filter_setting('new');
    if ($new_settings['enabled']) {
      $filters['new'] = $new_settings['title'] ?: 'New Products';
    }

    $discounts_settings = $this->get_filter_setting('discounts');
    if ($discounts_settings['enabled']) {
      $filters['discounts'] = $discounts_settings['title'] ?: 'Discounts';
    }

    if (empty($filters)) {
      return '';
    }

    return $this->render_checkbox_list(
      'Filter',
      $filters,
      'filter',
      $current_filters
    );
  }

  public function render_product_taxonomies_filter(
    array $atts,
    string $section
  ): string {

    if ($section === 'product_tag') {
      return $this->render_simple_taxonomy_filter(
        'product_tag',
        'Product Tags',
        'product_tag'
      );
    }

    if ($section === 'product_cat') {
      return $this->render_simple_taxonomy_filter(
        'product_cat',
        'Product Categories',
        'product_cat'
      );
    }

    if ($section === 'product_brand') {
      return $this->render_simple_taxonomy_filter(
        'product_brand',
        'Product Brands',
        'product_brand'
      );
    }

    return '';
  }

  public function render_checkbox_list(
    string $title,
    array $items,
    string $input_name,
    array $selected_values = []
  ): string {
    if (empty($items)) {
      return '';
    }

    ob_start();
    ?>
    <div class="shop-filters-section">
      <ul class="filter-checkboxes">
        <?php foreach ($items as $value => $label): ?>
          <?php
          $is_checked = in_array($value, $selected_values);
          $checkbox_id = esc_attr($input_name . '_' . $value);
          ?>
          <li>
            <label for="<?php echo $checkbox_id; ?>">
              <input type="checkbox" id="<?php echo $checkbox_id; ?>" name="<?php echo esc_attr($input_name); ?>[]"
                value="<?php echo esc_attr($value); ?>" <?php checked($is_checked, true); ?>>
              <span>
                <?php echo esc_html($label); ?>
              </span>
            </label>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php
    return ob_get_clean();
  }

  private function get_filter_setting($key)
  {
    $defaults = [
      'enabled' => 1,
      'title' => '',
    ];

    $settings = get_option('b2bking_addons_shop_filters_settings', false);

    if ($settings === false) {
      return wp_parse_args(['enabled' => 1], $defaults);
    }

    if (isset($settings[$key])) {
      $enabled = isset($settings[$key]['enabled']) ? $settings[$key]['enabled'] : 0;
      return wp_parse_args(['enabled' => $enabled] + $settings[$key], $defaults);
    }

    return $defaults;
  }
}

<?php

namespace JPJULIAO\B2BKing_Addons;

class Shop_Filters_Renderer extends Shop_Filters_Base
{

  public function __construct()
  {
    add_shortcode('shop_filters', [$this, 'render_shortcode']);
  }

  public function render_shortcode(array $atts): string
  {
    $shop_url = get_permalink(wc_get_page_id('shop'));
    ob_start();
    ?>
    <form method="get" action="<?php echo esc_url($shop_url); ?>" class="shop-filters-form">
      <?php
      echo $this->render_product_best_new_discounts_filter($atts);
      echo $this->render_product_taxonomies_filter($atts);
      echo $this->render_product_attributes_filter($atts);
      ?>
      <div class="shop-filters-control">
        <button type="submit" class="apply-filters-btn">Apply Filters</button>
      </div>
    </form>
    <?php
    return ob_get_clean();
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
      'Filter Products:',
      $filters,
      'filter',
      $current_filters
    );
  }

  public function render_product_taxonomies_filter(array $atts): string
  {
    $outputs = [];

    $tag_settings = $this->get_filter_setting('product_tag');
    if ($tag_settings['enabled']) {
      $outputs[] = $this->render_simple_taxonomy_filter(
        'product_tag',
        $tag_settings['title'] ?: 'Product Tags:',
        'product_tag'
      );
    }

    $cat_settings = $this->get_filter_setting('product_cat');
    if ($cat_settings['enabled']) {
      $outputs[] = $this->render_simple_taxonomy_filter(
        'product_cat',
        $cat_settings['title'] ?: 'Product Categories:',
        'product_cat'
      );
    }

    $brand_settings = $this->get_filter_setting('product_brand');
    if ($brand_settings['enabled']) {
      $outputs[] = $this->render_simple_taxonomy_filter(
        'product_brand',
        $brand_settings['title'] ?: 'Brands:',
        'product_brand'
      );
    }

    return implode('', $outputs);
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
    <div class="shop-filters-control">
      <h4>
        <?php echo esc_html($title); ?>
      </h4>
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

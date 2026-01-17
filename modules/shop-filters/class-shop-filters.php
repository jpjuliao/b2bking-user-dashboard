<?php
/**
 * WooCommerce Shop Page Product Filters Class
 * Features: URL-based filters + Sidebar shortcode control (no styling)
 * Shortcode: [shop_filters]
 * Namespace: JPJULIAO\B2BKing_Addons
 */

namespace JPJULIAO\B2BKing_Addons;

class Shop_Filters
{

  public function __construct()
  {
    add_action('pre_get_posts', array($this, 'filter_shop_query'));
    add_shortcode('shop_filters', array($this, 'render_shortcode'));
    add_filter('request', array($this, 'clean_filter_url'));
  }

  public function render_shortcode(array $atts): string
  {
    $shop_url = get_permalink(wc_get_page_id('shop'));
    ob_start();
    ?>
    <form method="get" action="<?php echo esc_url($shop_url); ?>" class="shop-filters-form">
      <?php
      echo $this->render_price_filter($atts);
      echo $this->render_best_new_discounts_filter($atts);
      echo $this->render_brands_filter($atts);
      echo $this->render_product_tags_filter($atts);
      echo $this->render_product_cats_filter($atts);
      ?>
      <div class="shop-filters-control">
        <button type="submit" class="apply-filters-btn">Apply Filters</button>
      </div>
    </form>
    <?php
    return ob_get_clean();
  }

  public function clean_filter_url(array $query_vars): array
  {
    // Clean the REQUEST_URI by removing %5B%5D (encoded [])
    if (isset($_SERVER['REQUEST_URI'])) {
      $clean_uri = str_replace(['%5B', '%5D'], '', $_SERVER['REQUEST_URI']);

      // Only redirect if the URI actually changed
      if ($clean_uri !== $_SERVER['REQUEST_URI']) {
        wp_redirect(home_url($clean_uri), 301);
        exit;
      }

      // Parse query string to handle multiple values for the same parameter
      $query_string = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
      if ($query_string) {
        $params = [];
        parse_str($query_string, $params);

        // Manually parse to get ALL values (parse_str only gets the last one)
        $query_parts = explode('&', $query_string);
        $multi_params = [];

        foreach ($query_parts as $part) {
          if (strpos($part, '=') !== false) {
            list($key, $value) = explode('=', $part, 2);
            $key = urldecode($key);
            $value = urldecode($value);

            if (!isset($multi_params[$key])) {
              $multi_params[$key] = [];
            }
            $multi_params[$key][] = $value;
          }
        }

        // Update $_GET with arrays for parameters that have multiple values
        foreach ($multi_params as $key => $values) {
          if (count($values) > 1) {
            $_GET[$key] = $values;
          } elseif (count($values) === 1) {
            $_GET[$key] = $values[0];
          }
        }
      }
    }

    return $query_vars;
  }

  public function filter_shop_query(\WP_Query $query): void
  {
    if (!$query->is_main_query() || !is_shop()) {
      return;
    }

    $filters = isset($_GET['filter']) ? (array) $_GET['filter'] : [];
    $filters = array_map('sanitize_text_field', $filters);

    if (in_array('best', $filters)) {
      $query->set('meta_key', 'total_sales');
      $query->set('orderby', 'meta_value_num');
      $query->set('order', 'DESC');
    } elseif (in_array('new', $filters)) {
      $date_query = array(
        array(
          'after' => '30 days ago',
          'inclusive' => true,
        ),
      );
      $query->set('date_query', $date_query);
      $query->set('orderby', 'date');
      $query->set('order', 'DESC');
    } elseif (in_array('discounts', $filters)) {
      $meta_query = WC()->query->get_meta_query();
      $meta_query[] = array(
        'key' => '_sale_price',
        'value' => 0,
        'compare' => '>',
        'type' => 'numeric',
      );
      $query->set('meta_query', $meta_query);
    }

    // Get existing tax query from WooCommerce
    $tax_query = $query->get('tax_query');
    if (!is_array($tax_query)) {
      $tax_query = [];
    }

    $brand_filters = isset($_GET['brand']) ? (array) $_GET['brand'] : [];
    $brand_filters = array_map('sanitize_text_field', $brand_filters);
    if (!empty($brand_filters)) {
      $tax_query[] = array(
        'taxonomy' => 'product_brand',
        'field' => 'slug',
        'terms' => $brand_filters,
        'operator' => 'IN',
      );
    }

    $tag_filters = isset($_GET['product_tag']) ? (array) $_GET['product_tag'] : [];
    $tag_filters = array_map('sanitize_text_field', $tag_filters);
    if (!empty($tag_filters)) {
      $tax_query[] = array(
        'taxonomy' => 'product_tag',
        'field' => 'slug',
        'terms' => $tag_filters,
        'operator' => 'IN',
      );
    }

    $cat_filters = isset($_GET['product_cat']) ? (array) $_GET['product_cat'] : [];
    $cat_filters = array_map('sanitize_text_field', $cat_filters);
    if (!empty($cat_filters)) {
      $tax_query[] = array(
        'taxonomy' => 'product_cat',
        'field' => 'slug',
        'terms' => $cat_filters,
        'operator' => 'IN',
      );
    }

    // Only set tax_query if we have custom filters
    if (count($tax_query) > 0) {
      // Set relation if we have multiple taxonomy queries
      if (!isset($tax_query['relation'])) {
        $tax_query['relation'] = 'AND';
      }
      $query->set('tax_query', $tax_query);
    }
  }

  public function render_best_new_discounts_filter(array $atts): string
  {
    $current_filters = isset($_GET['filter']) ? (array) $_GET['filter'] : [];
    $current_filters = array_map('sanitize_text_field', $current_filters);

    $filters = array(
      'best' => 'Best Sellers',
      'new' => 'New Products',
      'discounts' => 'Discounts',
    );

    return $this->render_checkbox_list('Filter Products:', $filters, 'filter', $current_filters);
  }

  public function render_product_tags_filter(array $atts): string
  {
    $current_filters = isset($_GET['product_tag']) ? (array) $_GET['product_tag'] : [];
    $current_filters = array_map('sanitize_text_field', $current_filters);

    $product_tags = get_terms([
      'taxonomy' => 'product_tag',
      'hide_empty' => false,
    ]);

    $tags_array = [];
    foreach ($product_tags as $tag) {
      $tags_array[$tag->slug] = $tag->name;
    }

    return $this->render_checkbox_list('Product Tags:', $tags_array, 'product_tag', $current_filters);
  }

  public function render_product_cats_filter(array $atts): string
  {
    $current_filters = isset($_GET['product_cat']) ? (array) $_GET['product_cat'] : [];
    $current_filters = array_map('sanitize_text_field', $current_filters);

    $product_cats = get_terms([
      'taxonomy' => 'product_cat',
      'hide_empty' => false,
    ]);

    $cats_array = [];
    foreach ($product_cats as $cat) {
      $cats_array[$cat->slug] = $cat->name;
    }

    return $this->render_checkbox_list('Product Categories:', $cats_array, 'product_cat', $current_filters);
  }

  public function render_brands_filter(array $atts): string
  {
    $current_filters = isset($_GET['brand']) ? (array) $_GET['brand'] : [];
    $current_filters = array_map('sanitize_text_field', $current_filters);

    $brands = get_terms([
      'taxonomy' => 'product_brand',
      'hide_empty' => false,
    ]);

    $brands_array = [];
    foreach ($brands as $brand) {
      $brands_array[$brand->slug] = $brand->name;
    }

    return $this->render_checkbox_list('Brands:', $brands_array, 'brand', $current_filters);
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
              <span><?php echo esc_html($label); ?></span>
            </label>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php
    return ob_get_clean();
  }

  public function render_price_filter(array $atts): string
  {
    $products = get_posts([
      'post_type' => 'product',
      'posts_per_page' => -1,
    ]);
    $max_price = 0;
    $min_price = 0;

    // Get all price and merge duplicates
    $prices = array_filter(array_unique(array_map(function ($product) {
      return get_post_meta($product->ID, '_price', true);
    }, $products)));
    $max_price = max($prices);
    $min_price = min($prices);

    ob_start();
    ?>
    <div class="shop-filters-control">
      <h4>Price:</h4>
      <span>$
        <?php echo esc_html($min_price); ?>
      </span>
      <input type="range" name="price_min" min="<?php echo esc_attr($min_price); ?>"
        max="<?php echo esc_attr($max_price); ?>" value="<?php echo esc_attr($min_price); ?>">
      <input type="range" name="price_max" min="<?php echo esc_attr($min_price); ?>"
        max="<?php echo esc_attr($max_price); ?>" value="<?php echo esc_attr($max_price); ?>">
      <span>$
        <?php echo esc_html($max_price); ?>
      </span>
    </div>
    <?php
    return ob_get_clean();
  }
}

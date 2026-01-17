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
  }

  public function render_shortcode($atts): string
  {
    return implode('', [
      $this->render_best_new_discounts_filter($atts),
      $this->render_price_filter($atts),
      $this->render_brands_filter($atts),
      $this->render_product_tags_filter($atts),
      $this->render_product_cats_filter($atts),
    ]);
  }

  public function filter_shop_query($query): void
  {
    if (!$query->is_main_query() || !is_shop()) {
      return;
    }

    $filter = isset($_GET['filter'])
      ? sanitize_text_field($_GET['filter']) : '';

    switch ($filter) {
      case 'best':
        $query->set('meta_key', 'total_sales');
        $query->set('orderby', 'meta_value_num');
        $query->set('order', 'DESC');
        break;

      case 'new':
        $date_query = array(
          array(
            'after' => '30 days ago',
            'inclusive' => true,
          ),
        );
        $query->set('date_query', $date_query);
        $query->set('orderby', 'date');
        $query->set('order', 'DESC');
        break;

      case 'discounts':
        $meta_query = WC()->query->get_meta_query();
        $meta_query[] = array(
          'key' => '_sale_price',
          'value' => 0,
          'compare' => '>',
          'type' => 'numeric',
        );
        $query->set('meta_query', $meta_query);
        break;
    }
  }

  public function render_best_new_discounts_filter($atts): string
  {
    $shop_url = get_permalink(wc_get_page_id('shop'));
    $current_filter = isset($_GET['filter'])
      ? sanitize_text_field($_GET['filter']) : '';

    $filters = array(
      '' => 'All Products',
      'best' => 'Best Sellers',
      'new' => 'New Products',
      'discounts' => 'Discounts',
    );

    ob_start();
    ?>
    <div class="shop-filters-control">
      <h4>Filter Products:</h4>
      <ul class="filter-buttons">
        <?php foreach ($filters as $filter_key => $filter_label): ?>
          <?php
          $filter_url = $filter_key === ''
            ? remove_query_arg('filter', $shop_url)
            : add_query_arg('filter', $filter_key, $shop_url);
          $is_active = $current_filter === $filter_key;
          ?>
          <li>
            <a href="<?php echo esc_url($filter_url); ?>" class="filter-btn <?php echo $is_active ? 'active' : ''; ?>">
              <?php if ($is_active): ?>
                <strong><?php echo esc_html($filter_label); ?></strong>
              <?php else: ?>
                <?php echo esc_html($filter_label); ?>
              <?php endif; ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php
    return ob_get_clean();
  }

  public function render_product_tags_filter($atts): string
  {
    $shop_url = get_permalink(wc_get_page_id('shop'));
    $current_filter = isset($_GET['filter'])
      ? sanitize_text_field($_GET['filter']) : '';

    $product_tags = get_terms([
      'taxonomy' => 'product_tag',
      'hide_empty' => false,
    ]);

    ob_start();
    ?>
    <div class="shop-filters-control">
      <h4>Product Tags:</h4>
      <ul class="filter-buttons">
        <?php foreach ($product_tags as $product_tag): ?>
          <?php
          $filter_url = $product_tag->slug === ''
            ? remove_query_arg('filter', $shop_url)
            : add_query_arg('filter', $product_tag->slug, $shop_url);
          $is_active = $current_filter === $product_tag->slug;
          ?>
          <li>
            <a href="<?php echo esc_url($filter_url); ?>" class="filter-btn <?php echo $is_active ? 'active' : ''; ?>">
              <?php if ($is_active): ?>
                <strong><?php echo esc_html($product_tag->name); ?></strong>
              <?php else: ?>
                <?php echo esc_html($product_tag->name); ?>
              <?php endif; ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php
    return ob_get_clean();
  }

  public function render_product_cats_filter($atts): string
  {
    $shop_url = get_permalink(wc_get_page_id('shop'));
    $current_filter = isset($_GET['filter'])
      ? sanitize_text_field($_GET['filter']) : '';

    $product_cats = get_terms([
      'taxonomy' => 'product_cat',
      'hide_empty' => false,
    ]);

    ob_start();
    ?>
    <div class="shop-filters-control">
      <h4>Product Categories:</h4>
      <ul class="filter-buttons">
        <?php foreach ($product_cats as $product_cat): ?>
          <?php
          $filter_url = $product_cat->slug === ''
            ? remove_query_arg('filter', $shop_url)
            : add_query_arg('filter', $product_cat->slug, $shop_url);
          $is_active = $current_filter === $product_cat->slug;
          ?>
          <li>
            <a href="<?php echo esc_url($filter_url); ?>" class="filter-btn <?php echo $is_active ? 'active' : ''; ?>">
              <?php if ($is_active): ?>
                <strong><?php echo esc_html($product_cat->name); ?></strong>
              <?php else: ?>
                <?php echo esc_html($product_cat->name); ?>
              <?php endif; ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php
    return ob_get_clean();
  }
  public function render_brands_filter($atts): string
  {
    $shop_url = get_permalink(wc_get_page_id('shop'));
    $current_filter = isset($_GET['filter'])
      ? sanitize_text_field($_GET['filter']) : '';

    $brands = get_terms([
      'taxonomy' => 'product_brand',
      'hide_empty' => false,
    ]);

    ob_start();
    ?>
    <div class="shop-filters-control">
      <h4>Brands:</h4>
      <ul class="filter-buttons">
        <?php foreach ($brands as $brand): ?>
          <?php
          $filter_url = $brand->slug === ''
            ? remove_query_arg('filter', $shop_url)
            : add_query_arg('filter', $brand->slug, $shop_url);
          $is_active = $current_filter === $brand->slug;
          ?>
          <li>
            <a href="<?php echo esc_url($filter_url); ?>" class="filter-btn <?php echo $is_active ? 'active' : ''; ?>">
              <?php if ($is_active): ?>
                <strong><?php echo esc_html($brand->name); ?></strong>
              <?php else: ?>
                <?php echo esc_html($brand->name); ?>
              <?php endif; ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php
    return ob_get_clean();
  }

  public function render_price_filter($atts): string
  {
    $shop_url = get_permalink(wc_get_page_id('shop'));
    $current_filter = isset($_GET['filter'])
      ? sanitize_text_field($_GET['filter']) : '';


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
      <span>$ <?php echo esc_html($min_price); ?></span>
      <input type="range" name="price_min" min="<?php echo esc_attr($min_price); ?>"
        max="<?php echo esc_attr($max_price); ?>" value="<?php echo esc_attr($min_price); ?>">
      <input type="range" name="price_max" min="<?php echo esc_attr($min_price); ?>"
        max="<?php echo esc_attr($max_price); ?>" value="<?php echo esc_attr($max_price); ?>">
      <span>$ <?php echo esc_html($max_price); ?></span>
    </div>
    <?php
    return ob_get_clean();
  }
}

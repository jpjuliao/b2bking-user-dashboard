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
    add_shortcode('shop_filters', array($this, 'render_filter_shortcode'));
  }

  /**
   * Filter shop page query based on URL parameter
   */
  public function filter_shop_query($query)
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

  /**
   * Shortcode for filter controls [shop_filters]
   * Pure UI elements + business logic only
   */
  public function render_filter_shortcode($atts)
  {
    $shop_url = get_permalink(wc_get_page_id('shop'));
    $current_filter = isset($_GET['filter'])
      ? sanitize_text_field($_GET['filter']) : '';

    // Define available filters
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
              <?php if ($is_active): ?><strong><?php endif; ?>
                <?php echo esc_html($filter_label); ?>
                <?php if ($is_active): ?></strong><?php endif; ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
      <br>
      <hr>
    </div>
    <?php
    return ob_get_clean();
  }
}

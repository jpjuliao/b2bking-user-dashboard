<?php

namespace JPJULIAO\B2BKing_Addons;

class Shop_Filters_Query extends Shop_Filters_Base
{
  public function __construct()
  {
    add_action('pre_get_posts', array($this, 'filter_shop_query'));
  }

  public function filter_shop_query(\WP_Query $query): void
  {
    if (!$query->is_main_query() || !is_shop()) {
      return;
    }

    $filters = $this->get_filter_values('filter');

    if (in_array('discounts', $filters)) {
      $this->apply_discounts_filter($query);
    }

    if (in_array('new', $filters)) {
      $this->apply_new_products_filter($query);
    }

    $this->apply_sorting_filters($query, $filters);
  }

  protected function apply_discounts_filter(\WP_Query $query): void
  {
    $meta_query = $query->get('meta_query');
    if (empty($meta_query)) {
      $meta_query = WC()->query->get_meta_query();
    }

    $meta_query[] = array(
      'key' => '_sale_price',
      'value' => 0,
      'compare' => '>',
      'type' => 'numeric',
    );

    $query->set('meta_query', $meta_query);
  }

  protected function apply_new_products_filter(\WP_Query $query): void
  {
    $date_query = $query->get('date_query');
    if (!is_array($date_query)) {
      $date_query = [];
    }

    $date_query[] = array(
      'after' => '30 days ago',
      'inclusive' => true,
    );

    $query->set('date_query', $date_query);
  }

  protected function apply_sorting_filters(\WP_Query $query, array $filters): void
  {
    if (in_array('best', $filters)) {
      $query->set('meta_key', 'total_sales');
      $query->set('orderby', 'meta_value_num');
      $query->set('order', 'DESC');

    } elseif (in_array('new', $filters)) {
      $query->set('orderby', 'date');
      $query->set('order', 'DESC');
    }
  }
}

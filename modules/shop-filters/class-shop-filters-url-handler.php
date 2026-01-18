<?php

namespace JPJULIAO\B2BKing_Addons;

class Shop_Filters_URL_Handler extends Shop_Filters_Base
{

  public function __construct()
  {
    add_filter('request', array($this, 'clean_filter_url'));
  }

  public function clean_filter_url(array $query_vars): array
  {
    $must_redirect = false;
    $new_params = $_GET;
    $filter_keys = ['filter', 'product_brand', 'product_tag', 'product_cat'];

    foreach ($filter_keys as $key) {
      if (isset($_GET[$key]) && is_array($_GET[$key])) {

        $new_params[$key] = implode(',', array_map('urlencode', $_GET[$key]));
        $must_redirect = true;
      }
    }

    if ($must_redirect) {
      $redirect_url = add_query_arg($new_params);
      wp_redirect($redirect_url);
      exit;
    }

    return $query_vars;
  }
}

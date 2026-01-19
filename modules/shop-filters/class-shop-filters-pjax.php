<?php

namespace JPJULIAO\B2BKing_Addons;

class Shop_Filters_Pjax extends Shop_Filters_Base
{
  public function __construct()
  {

    add_action(
      'wp_enqueue_scripts',
      [$this, 'pjax_cripts'],
      31
    );
  }

  public function pjax_cripts()
  {

    if (!function_exists('\woodmart_get_localized_string_array')) {
      return;
    }

    wp_enqueue_script(
      'b2bking-addons-shop-filters-pjax',
      PLUGIN_URL . '/assets/js/pjaxFilters.js',
      ['jquery'],
      '1.0.0',
      true
    );

    wp_localize_script(
      'b2bking-addons-shop-filters-pjax',
      'woodmart_settings',
      \woodmart_get_localized_string_array()
    );
  }

}
<?php
namespace JPJULIAO\B2BKing_Addons;

class B2B_Info_Table
{
  public function __construct()
  {
    add_action('template_redirect', [$this, 'init_hooks']);
  }

  public function init_hooks(): void
  {
    if (!is_b2b_user()) {
      return;
    }

    add_filter(
      'b2bking_information_table_content_rows',
      [$this, 'b2b_info_table']
    );
  }

  public function b2b_info_table($rows_array): array
  {

    $is_percentage_tiered = intval(
      get_option('b2bking_enter_percentage_tiered_setting', 0)
    ) === 1;

    if (!$is_percentage_tiered) {
      return $rows_array;
    }

    // Get price
    $price = get_post_meta(
      get_the_ID(),
      '_price',
      true
    );

    foreach ($rows_array as $key => $row) {
      $row_values = explode(':', $row, 2);
      if (sizeof($row_values) === 2) {
        $row_values[1] = str_replace('%', '', $row_values[1]);
        $discounted_price = $price - ($price * ($row_values[1] / 100));
        $row_values[1] = number_format($discounted_price, 2, '.', '');
        $rows_array[$key] = implode(':', $row_values);
      }
    }
    return $rows_array;
  }

}
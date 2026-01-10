<?php
namespace JPJULIAO\B2BKing\User_Dashboard;

class Guest_Info_Table
{

  public function __construct()
  {
    add_action('woocommerce_single_product_summary', [$this, 'info_table']);
  }

  public function info_table()
  {
    global $post;
    $b2bgroups = get_posts(['post_type' => 'b2bking_group']);
    $first_b2bgroup = $b2bgroups[0];
    $meta = get_post_meta(
      $post->ID,
      'b2bking_product_customrows_group_' . $first_b2bgroup->ID,
      true
    );
    ?>
    <div class="custom-info-table">
      <table class="shop_table b2bking_shop_table b2bking_information_table">
        <thead>
          <tr>
            <th><?php esc_html_e('Information Table', 'b2bking'); ?></th>
            <th></th>
          </tr>
        </thead>
        <tbody>
        <tbody>
          <?php
          $customrows = str_replace('&amp;', '&', $meta);
          $rows_array = explode(';', $customrows);
          $rows_array = apply_filters('b2bking_information_table_content_rows', $rows_array);
          foreach ($rows_array as $row) {
            $row_values = explode(':', $row, 2);
            if (!empty($row_values[0]) && !empty($row_values[1])) {
              // display row
              ?>
              <tr>
                <td>
                  <?php echo wp_kses(
                    $row_values[0],
                    array(
                      'br' => true,
                      'strong' => true,
                      'b' => true,
                      'a' => array('href' => array(), 'target' => array())
                    )
                  ); ?>
                </td>
                <td class="locked">
                  <span class="vc_icon_element-icon fa fa-solid fa-lock"></span>
                </td>
              </tr>
              <?php
            }
          }
          ?>
        </tbody>
      </table>
      <div><a href="/sign-up" class="btn btn-style-default btn-shape-rectangle btn-size-default">Sing up to see prices</a>
      </div>
    </div>
    <?php
  }

}
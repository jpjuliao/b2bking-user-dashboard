<?php

namespace JPJULIAO\B2BKing\User_Dashboard;

class UI
{
  public function __construct()
  {

    add_action(
      'woocommerce_account_dashboard',
      [$this, 'reports_page_content'],
      5
    );

    add_action(
      'woocommerce_account_dashboard',
      [$this, 'popular_products'],
      5
    );
  }

  public function reports_page_content()
  {

    // preloader if not in ajax - in ajax preloader is added via JS for smoother animations
    if (!wp_doing_ajax()) {
      ?>
      <div class="b2bkingpreloader">
        <img class="b2bking_loader_icon_button"
          src="<?php echo esc_attr(plugins_url('./includes/assets/images/loaderpagegold5.svg', __FILE__)); ?>">
      </div>
      <?php
    }

    ?>
    <div id="b2bking_dashboard_wrapper">
      <div class="b2bking_dashboard_page_wrapper b2bking_reports_page_wrapper">
        <div class="">
          <div class="d-md-flex align-items-center">
            <div>
              <h3 class="card-title"><?php esc_html_e('Sales Reports', 'b2bking'); ?></h3>
              <h5 class="card-subtitle"><?php esc_html_e('Total Sales Value', 'b2bking'); ?></h5>
            </div>
            <div class="ml-auto d-flex no-block align-items-center">
              <ul class="list-inline font-12 dl m-r-15 m-b-0 b2bking_reports_chart_info">
                <li class="list-inline-item text-primary"><i class="mdi mdi-checkbox-blank-circle"></i>
                  <?php esc_html_e('Gross Sales', 'b2bking'); ?></li>
                <li class="list-inline-item text-cyan"><i class="mdi mdi-checkbox-blank-circle"></i>
                  <?php esc_html_e('Net Sales', 'b2bking'); ?></li>
                <li class="list-inline-item text-info"><i class="mdi mdi-checkbox-blank-circle"></i>
                  <?php esc_html_e('Number of Orders', 'b2bking'); ?></li>

              </ul>
              <div class="b2bking_reports_topright_container">
                <div class="dl b2bking_reports_topright">
                  <div class="b2bking_reports_fromto">
                    <div class="b2bking_reports_fromto_text"><?php esc_html_e('From:', 'b2bking'); ?></div>
                    <input type="date" id="b2bking_reports_date_input_from"
                      class="b2bking_reports_date_input b2bking_reports_date_input_from">
                  </div>
                  <div class="b2bking_reports_fromto">
                    <div class="b2bking_reports_fromto_text"><?php esc_html_e('To:', 'b2bking'); ?></div>
                    <input type="date" class="b2bking_reports_date_input b2bking_reports_date_input_to">
                  </div>
                </div>
                <div id="b2bking_reports_quick_links">
                  <div class="b2bking_reports_linktext"><?php esc_html_e('Quick Select:', 'b2bking'); ?></div>
                  <a id="b2bking_reports_link_thismonth" hreflang="thismonth"
                    class="b2bking_reports_link"><?php esc_html_e('This Month', 'b2bking'); ?></a>
                  <a hreflang="lastmonth" class="b2bking_reports_link"><?php esc_html_e('Last Month', 'b2bking'); ?></a>
                  <a hreflang="thisyear" class="b2bking_reports_link"><?php esc_html_e('This Year', 'b2bking'); ?></a>
                  <a hreflang="lastyear" class="b2bking_reports_link"><?php esc_html_e('Last Year', 'b2bking'); ?></a>
                </div>
              </div>


            </div>
          </div>
          <div class="row">
            <!-- column -->
            <div class="col-lg-3">
              <h1 class="b2bking_total_b2b_sales_today m-b-0 m-t-30"><?php echo 0 ?></h1>
              <h6 class="font-light text-muted"><?php esc_html_e('Sales', 'b2bking'); ?></h6>
              <h3 class="b2bking_number_orders_today m-t-30 m-b-0"><?php echo 0; ?></h3>
              <h6 class="font-light text-muted"><?php esc_html_e('Orders', 'b2bking'); ?></h6>
              <a id="b2bking_export_report_button" class="btn btn-info m-t-20 p-15 p-l-25 p-r-25 m-b-20"
                href="javascript:void(0)"><?php esc_html_e('Export Report File', 'b2bking'); ?></a>
            </div>
            <!-- column -->
            <img class="b2bking_reports_icon_loader"
              src="<?php echo esc_attr(plugins_url('./includes/assets/images/loaderpagegold5.svg', __FILE__)); ?>">
            <div class="col-lg-9">
              <div class="campaign ct-charts"></div>
            </div>
            <div class="col-lg-3">
            </div>
            <div class="col-lg-9">
              <div class="campaign2 ct-charts"></div>
            </div>
            <!-- column -->
          </div>
        </div>
        <!-- ============================================================== -->
        <!-- Info Box -->
        <!-- ============================================================== -->
        <div class="card-body border-top border-bottom">
          <div class="row m-b-0" id="b2bking_reports_first_row">
            <!-- col -->
            <div class="col-lg-3 col-md-6">
              <div class="d-flex align-items-center">
                <div class="m-r-10"><span class="text-orange display-5"><i class="mdi mdi-cart"></i></span></div>
                <div><span><?php esc_html_e('Gross Sales', 'b2bking'); ?></span>
                  <h3 class="b2bking_reports_gross_sales font-medium m-b-0"><?php echo 0; ?></h3>
                </div>
              </div>
            </div>
            <!-- col -->
            <div class="col-lg-3 col-md-6">
              <div class="d-flex align-items-center">
                <div class="m-r-10"><span class="text-cyan display-5"><i class="mdi mdi-cart-outline"></i></i></span></div>
                <div><span><?php esc_html_e('Net Sales', 'b2bking'); ?></span>
                  <h3 class="b2bking_reports_net_sales font-medium m-b-0">
                    <?php echo 0; ?>
                  </h3>
                </div>
              </div>
            </div>
            <!-- col -->
            <div class="col-lg-3 col-md-6">
              <div class="d-flex align-items-center">
                <div class="m-r-10"><span class="text-info display-5"><i class="mdi mdi-package-variant"></i></span>
                </div>
                <div><span><?php esc_html_e('Orders Placed', 'b2bking'); ?></span>
                  <h3 class="b2bking_reports_number_orders font-medium m-b-0"><?php echo 0; ?></h3>
                </div>
              </div>
            </div>

            <!-- col -->
            <div class="col-lg-3 col-md-6">
              <div class="d-flex align-items-center">
                <div class="m-r-10"><span class="text-primary display-5"><i class="mdi mdi-tag-multiple"></i></i></span>
                </div>
                <div><span><?php esc_html_e('Items Purchased', 'b2bking'); ?></span>
                  <h3 class="b2bking_reports_items_purchased font-medium m-b-0"><?php echo 0; ?></h3>
                </div>
              </div>
            </div>
            <!-- col -->
          </div>
          <div class="row m-b-0" id="b2bking_reports_second_row">
            <!-- col -->
            <div class="col-lg-3 col-md-6">
              <div class="d-flex align-items-center">
                <div class="m-r-10"><span class="text-orange display-5"><i class="mdi mdi-shopping"></i></span>
                </div>
                <div><span><?php esc_html_e('Average Order Value', 'b2bking'); ?></span>
                  <h3 class="b2bking_reports_average_order_value font-medium m-b-0"><?php echo 0; ?></h3>
                </div>
              </div>
            </div>
            <!-- col -->
            <div class="col-lg-3 col-md-6">
              <div class="d-flex align-items-center">
                <div class="m-r-10"><span class="text-cyan display-5"><i class="mdi mdi-credit-card-off"></i></i></span>
                </div>
                <div><span><?php esc_html_e('Refund Amount', 'b2bking'); ?></span>
                  <h3 class="b2bking_reports_refund_amount font-medium m-b-0">
                    <?php echo 0; ?>
                  </h3>
                </div>
              </div>
            </div>
            <!-- col -->
            <div class="col-lg-3 col-md-6">
              <div class="d-flex align-items-center">
                <div class="m-r-10"><span class="text-info display-5"><i class="mdi mdi-ticket-percent"></i></span>
                </div>
                <div><span><?php esc_html_e('Coupons Used', 'b2bking'); ?></span>
                  <h3 class="b2bking_reports_coupons_amount font-medium m-b-0"><?php echo 0; ?></h3>
                </div>
              </div>
            </div>

            <!-- col -->
            <div class="col-lg-3 col-md-6">
              <div class="d-flex align-items-center">
                <div class="m-r-10"><span class="text-primary display-5"><i class="mdi mdi-truck-delivery"></i></i></span>
                </div>
                <div><span><?php esc_html_e('Shipping Charges', 'b2bking'); ?></span>
                  <h3 class="b2bking_reports_shipping_charges font-medium m-b-0"><?php echo 0; ?></h3>
                </div>
              </div>
            </div>
            <!-- col -->
          </div>
        </div>
      </div>
    </div>
    <table id="b2bking_admin_reports_export_table" style="display:none">
      <thead>
        <tr>
          <th><?php esc_html_e('Date', 'b2bking'); ?></th>
          <th><?php esc_html_e('Gross Sales', 'b2bking'); ?></th>
          <th><?php esc_html_e('Net Sales', 'b2bking'); ?></th>
          <th><?php esc_html_e('Number of Orders', 'b2bking'); ?></th>
          <th><?php esc_html_e('Number of Items', 'b2bking'); ?></th>
          <th><?php esc_html_e('Refund Amount', 'b2bking'); ?></th>
          <th><?php esc_html_e('Worth of Coupons', 'b2bking'); ?></th>
          <th><?php esc_html_e('Shipping Charges', 'b2bking'); ?></th>
        </tr>
      </thead>
      <tbody>

      </tbody>
    </table>
    <?php

  }

  public function popular_products()
  {
    $products = [];
    $id = get_current_user_id();
    $orders = \wc_get_orders(array(
      'customer_id' => $id,
      'limit' => -1,
    ));
    foreach ($orders as $order) {
      foreach ($order->get_items() as $item) {
        $product_id = $item->get_product_id();
        $quantity = $item->get_quantity();

        if (isset($products[$product_id])) {
          $products[$product_id] += $quantity;
        } else {
          $products[$product_id] = $quantity;
        }
      }
    }
    if (empty($products)) {
      return;
    }
    echo '<div class="b2bk_user_dashboard_popular_products">';
    echo '<h3>' . esc_html__('Popular Products', 'b2bking') . '</h3>';
    echo '<table>';
    echo '<thead>';
    echo '<tr>';
    echo '<th>' . esc_html__('Name', 'b2bking') . '</th>';
    echo '<th>' . esc_html__('Quantity', 'b2bking') . '</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    foreach ($products as $product_id => $quantity) {
      $link = get_the_permalink($product_id);
      echo '<tr>';
      echo '<td><a href="' . $link . '">' . get_the_title($product_id) . '</a></td>';
      echo '<td>' . $quantity . '</td>';
      echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
  }
}
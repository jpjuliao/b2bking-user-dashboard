<?php

class JPJULIAO_B2bking_Reports
{
  public function __construct()
  {
    add_action('wp_enqueue_scripts', function () {
      $this->enqueue_scripts();
      $this->localize_scripts();
    });
  }

  public function enqueue_scripts()
  {

    wp_enqueue_script(
      'b2bking_custom_global_script',
      plugins_url('assets/js/adminglobal.js', __FILE__),
      $deps = array(),
      // $deps = array('wp-color-picker'),
      $ver = B2BKING_VERSION,
      $in_footer = true
    );

    wp_enqueue_style(
      'b2bking_global_admin_style',
      plugins_url('assets/css/adminglobal.css', __FILE__),
      $deps = array(),
      $ver = B2BKING_VERSION
    );

    wp_enqueue_style(
      'b2bking_admin_dashboard',
      plugins_url('assets/dashboard/cssjs/dashboardstyle.min.css', __FILE__),
      $deps = array(),
      $ver = B2BKING_VERSION
    );

    wp_enqueue_script(
      'b2bking-popper',
      plugins_url('assets/lib/popper/popper.min.js', __FILE__)
    );

    wp_enqueue_script(
      'b2bking-tippy',
      plugins_url('assets/lib/popper/tippy.min.js', __FILE__)
    );

    wp_enqueue_script(
      'b2bking-sweetalert2',
      plugins_url('assets/lib/sweetalert/sweetalert2.all.min.js', __FILE__),
      $deps = array(),
      $ver = B2BKING_VERSION
    );

    wp_enqueue_script(
      'b2bking_global_admin_notice_script',
      plugins_url('assets/js/adminnotice.js', __FILE__),
      $deps = array(),
      $ver = B2BKING_VERSION,
      $in_footer = true
    );

    wp_enqueue_script(
      'dataTables',
      plugins_url('assets/lib/dataTables/jquery.dataTables.min.js', __FILE__),
      $deps = array(),
      $ver = false,
      $in_footer = true
    );

    wp_enqueue_style(
      'dataTables',
      plugins_url('assets/lib/dataTables/jquery.dataTables.min.css', __FILE__)
    );

    wp_enqueue_script(
      'dataTablesButtons',
      plugins_url('assets/lib/dataTables/dataTables.buttons.min.js', __FILE__),
      $deps = array(),
      $ver = false,
      $in_footer = true
    );

    wp_enqueue_script(
      'dataTablesButtonsHTML',
      plugins_url('assets/lib/dataTables/buttons.html5.min.js', __FILE__),
      $deps = array(),
      $ver = false,
      $in_footer = true
    );

    wp_enqueue_script(
      'dataTablesButtonsPrint',
      plugins_url('assets/lib/dataTables/buttons.print.min.js', __FILE__),
      $deps = array(),
      $ver = false,
      $in_footer = true
    );

    wp_enqueue_script(
      'dataTablesButtonsColvis',
      plugins_url('assets/lib/dataTables/buttons.colVis.min.js', __FILE__),
      $deps = array(),
      $ver = false,
      $in_footer = true
    );

    wp_enqueue_script(
      'jszip',
      plugins_url('assets/lib/dataTables/jszip.min.js', __FILE__),
      $deps = array(),
      $ver = false,
      $in_footer = true
    );

    wp_enqueue_script(
      'pdfmake',
      plugins_url('assets/lib/pdfmake/pdfmake.min.js', __FILE__),
      $deps = array(),
      $ver = false,
      $in_footer = true
    );

    wp_enqueue_script(
      'vfsfonts',
      plugins_url('assets/lib/pdfmake/vfs_fonts.js', __FILE__),
      $deps = array(),
      $ver = false,
      $in_footer = true
    );

    wp_enqueue_style(
      'wp-color-picker'
    );

    wp_enqueue_style(
      'chartist',
      plugins_url('assets/dashboard/chartist/chartist.min.css', __FILE__),
      $deps = array(),
      $ver = B2BKING_VERSION
    );

    wp_enqueue_script(
      'chartist',
      plugins_url('assets/dashboard/chartist/chartist.min.js', __FILE__),
      $deps = array(),
      $ver = B2BKING_VERSION,
      $in_footer = true
    );

    wp_enqueue_script(
      'chartist-plugin-tooltip',
      plugins_url('assets/dashboard/chartist/chartist-plugin-tooltip.min.js', __FILE__),
      $deps = array(),
      $ver = false,
      $in_footer = true
    );

    wp_enqueue_style(
      'select2',
      plugins_url('assets/lib/select2/select2.min.css', __FILE__)
    );

    wp_enqueue_script(
      'select2',
      plugins_url('assets/lib/select2/select2.min.js', __FILE__),
      array('jquery')
    );

  }

  public function localize_scripts()
  {

    $data = self::b2bking_get_dashboard_data();

    // Send data to JS
    $translation_array = array(
      'days_sales_b2b' => apply_filters('b2bking_dashboard_days_sales_b2b', $data['days_sales_array']),
      'days_sales_b2c' => apply_filters('b2bking_dashboard_days_sales_b2c', $data['days_sales_b2c_array']),
      'hours_sales_b2b' => array_values($data['hours_sales_array']),
      'hours_sales_b2c' => array_values($data['hours_sales_b2c_array']),
      'b2bking_demo' => apply_filters('b2bking_is_dashboard_demo', 0),
      'currency_symbol' => get_woocommerce_currency_symbol(),
    );

    wp_localize_script(
      'b2bking_custom_global_script',
      'b2bking_dashboard',
      $translation_array
    );

    // Send data to JS
    $data_js = array(
      'security' => wp_create_nonce('b2bking_notice_security_nonce'),
    );

    wp_localize_script(
      'b2bking_global_admin_notice_script',
      'b2bking_notice',
      $data_js
    );

    wp_localize_script(
      'b2bking_custom_global_script',
      'ajaxurl',
      array(
        'security' => wp_create_nonce('b2bking_security_nonce'),
        'ajaxurl' => admin_url('admin-ajax.php'),
      ),
    );
  }

  public static function b2bking_get_dashboard_data()
  {

    $data = array();

    $dashboarddata = get_transient('webwizards_dashboard_data_cache');
    if ($dashboarddata) {
      $data = $dashboarddata;

      $default_cache_time = get_option('b2bking_default_cache_time', 86400);

      // check cache time - clear every 12 hours
      $time = intval(get_transient('webwizards_dashboard_data_cache_time'));
      if ((time() - $time) > apply_filters('b2bking_cache_time_setting', $default_cache_time)) {
        // clear cache
        delete_transient('webwizards_dashboard_data_cache');
        delete_transient('webwizards_dashboard_data_cache_time');
        $dashboarddata = false;
        $data = array();
      }
    }

    if (!$dashboarddata) {

      // if this function was tried 2 times in less than 86400, automatically, change the default cache time (likely unable to get the data in a reasonable time)
      $current_check_time = time();
      $last_check_time = get_option('b2bking_last_check_cache_time', false);
      if ($last_check_time !== false) {
        if (($current_check_time - $last_check_time) < 86400) {
          update_option('b2bking_default_cache_time', 86400000);
        }
      }
      update_option('b2bking_last_check_cache_time', $current_check_time);

      // get all orders in past 31 days for calculations

      $date_to = get_date_from_gmt(date('Y-m-d H:i:s'), 'Y-m-d H:i:s');
      $date_from = get_date_from_gmt(date('Y-m-d H:i:s'), 'Y-m-d');
      $post_status = implode("','", apply_filters('b2bking_reports_statuses', array('wc-on-hold', 'wc-pending', 'wc-processing', 'wc-completed')));

      $args = array(
        'status' => apply_filters('b2bking_reports_statuses', array('wc-on-hold', 'wc-pending', 'wc-processing', 'wc-completed')),
        'date_created' => b2bking()->convert_date_from_to_range($date_from),
        'limit' => -1,
        'type' => 'shop_order',
      );
      $orders_today = wc_get_orders($args);


      $date_from = get_date_from_gmt(date('Y-m-d H:i:s', strtotime('-7 days')), 'Y-m-d');

      $args = array(
        'status' => apply_filters('b2bking_reports_statuses', array('wc-on-hold', 'wc-pending', 'wc-processing', 'wc-completed')),
        'date_created' => '>=' . b2bking()->convert_after_date_from_to_range($date_from),
        'limit' => -1,
        'type' => 'shop_order',

      );
      $orders_seven_days = wc_get_orders($args);


      $date_from = get_date_from_gmt(date('Y-m-d H:i:s', strtotime('-31 days')), 'Y-m-d');


      $args = array(
        'status' => apply_filters('b2bking_reports_statuses', array('wc-on-hold', 'wc-pending', 'wc-processing', 'wc-completed')),
        'date_created' => '>=' . b2bking()->convert_after_date_from_to_range($date_from),
        'limit' => -1,
        'type' => 'shop_order',

      );
      $orders_thirtyone_days = wc_get_orders($args);

      // if b2bking is in b2b mode, ignore whether user is B2B
      $plugin_status = get_option('b2bking_plugin_status_setting', 'b2b');

      // total b2b sales
      $total_b2b_sales_today = 0;
      $total_b2b_sales_seven_days = 0;
      $total_b2b_sales_thirtyone_days = 0;

      // total tax
      $tax_b2b_sales_today = 0;
      $tax_b2b_sales_seven_days = 0;
      $tax_b2b_sales_thirtyone_days = 0;

      // nr of orders
      $number_b2b_sales_today = 0;
      $number_b2b_sales_seven_days = 0;
      $number_b2b_sales_thirtyone_days = 0;

      // nr of unique customers
      $customers_b2b_sales_today = 0;
      $customers_b2b_sales_seven_days = 0;
      $customers_b2b_sales_thirtyone_days = 0;

      //calculate today
      $array_of_customers_ids = array();
      foreach ($orders_today as $order) {
        $order_user_id = $order->get_customer_id();

        if ($plugin_status === 'b2b') {
          $total_b2b_sales_today += self::convert_to_base_currency($order, $order->get_total());
          $tax_b2b_sales_today += self::convert_to_base_currency($order, $order->get_total_tax());
          $number_b2b_sales_today++;
          array_push($array_of_customers_ids, $order_user_id);

        } else {
          if (get_user_meta($order_user_id, 'b2bking_b2buser', true) === 'yes') {
            $total_b2b_sales_today += self::convert_to_base_currency($order, $order->get_total());
            $tax_b2b_sales_today += self::convert_to_base_currency($order, $order->get_total_tax());
            $number_b2b_sales_today++;
            array_push($array_of_customers_ids, $order_user_id);
          }
        }
      }
      $customers_b2b_sales_today = count(array_unique($array_of_customers_ids));

      //calculate seven days
      $array_of_customers_ids = array();
      foreach ($orders_seven_days as $order) {
        $order_user_id = $order->get_customer_id();

        if ($plugin_status === 'b2b') {
          $total_b2b_sales_seven_days += self::convert_to_base_currency($order, $order->get_total());
          $tax_b2b_sales_seven_days += self::convert_to_base_currency($order, $order->get_total_tax());
          $number_b2b_sales_seven_days++;
          array_push($array_of_customers_ids, $order_user_id);
        } else {
          // check user
          if (get_user_meta($order_user_id, 'b2bking_b2buser', true) === 'yes') {
            $total_b2b_sales_seven_days += self::convert_to_base_currency($order, $order->get_total());
            $tax_b2b_sales_seven_days += self::convert_to_base_currency($order, $order->get_total_tax());
            $number_b2b_sales_seven_days++;
            array_push($array_of_customers_ids, $order_user_id);
          }
        }
      }
      $customers_b2b_sales_seven_days = count(array_unique($array_of_customers_ids));

      //calculate thirtyone days
      $array_of_customers_ids = array();
      foreach ($orders_thirtyone_days as $order) {
        $order_user_id = $order->get_customer_id();

        if ($plugin_status === 'b2b') {
          $total_b2b_sales_thirtyone_days += self::convert_to_base_currency($order, $order->get_total());
          $tax_b2b_sales_thirtyone_days += self::convert_to_base_currency($order, $order->get_total_tax());
          $number_b2b_sales_thirtyone_days++;
          array_push($array_of_customers_ids, $order_user_id);
        } else {
          if (get_user_meta($order_user_id, 'b2bking_b2buser', true) === 'yes') {
            $total_b2b_sales_thirtyone_days += self::convert_to_base_currency($order, $order->get_total());
            $tax_b2b_sales_thirtyone_days += self::convert_to_base_currency($order, $order->get_total_tax());
            $number_b2b_sales_thirtyone_days++;
            array_push($array_of_customers_ids, $order_user_id);
          }
        }
      }
      $customers_b2b_sales_thirtyone_days = count(array_unique($array_of_customers_ids));

      // get each day in the past 31 days and form an array with day and total sales
      $i = 1;
      $days_sales_array = array();
      $days_sales_b2c_array = array();
      $hours_sales_b2c_array = $hours_sales_array = array(
        '00' => 0,
        '01' => 0,
        '02' => 0,
        '03' => 0,
        '04' => 0,
        '05' => 0,
        '06' => 0,
        '07' => 0,
        '08' => 0,
        '09' => 0,
        '10' => 0,
        '11' => 0,
        '12' => 0,
        '13' => 0,
        '14' => 0,
        '15' => 0,
        '16' => 0,
        '17' => 0,
        '18' => 0,
        '19' => 0,
        '20' => 0,
        '21' => 0,
        '22' => 0,
        '23' => 0,
      );

      while ($i < 32) {

        $date_from = $date_to = get_date_from_gmt(date('Y-m-d H:i:s', strtotime('-' . ($i - 1) . ' days')), 'Y-m-d');

        $args = array(
          'status' => apply_filters('b2bking_reports_statuses', array('wc-on-hold', 'wc-pending', 'wc-processing', 'wc-completed')),
          'date_created' => b2bking()->convert_date_from_to_range($date_from),
          'limit' => -1,
          'type' => 'shop_order',
        );
        $orders_day = wc_get_orders($args);

        //calculate totals
        $sales_total = 0;
        $sales_total_b2c = 0;
        foreach ($orders_day as $order) {
          $order_user_id = $order->get_customer_id();
          $order_total = self::convert_to_base_currency($order, $order->get_total());

          if ($plugin_status === 'b2b') {
            $sales_total += $order_total;
          } else {
            // check user
            if (get_user_meta($order_user_id, 'b2bking_b2buser', true) === 'yes') {
              $sales_total += $order_total;
            } else {
              $sales_total_b2c += $order_total;
            }
          }
        }

        // if first day, get this by hour
        if ($i === 1) {
          $date_to = get_date_from_gmt(date('Y-m-d H:i:s'), 'Y-m-d H:i:s');
          $date_from = get_date_from_gmt(date('Y-m-d H:i:s'), 'Y-m-d');

          $args = array(
            'status' => apply_filters('b2bking_reports_statuses', array('wc-on-hold', 'wc-pending', 'wc-processing', 'wc-completed')),
            'date_created' => '>=' . b2bking()->convert_after_date_from_to_range($date_from),
            'limit' => -1,
            'type' => 'shop_order',

          );
          $orders_seven_days = wc_get_orders($args);

          foreach ($orders_day as $order) {
            // get hour of the order
            $date = $order->get_date_created();
            $hour = explode(':', explode('T', $date)[1])[0];
            $order_total = self::convert_to_base_currency($order, $order->get_total());
            $order_user_id = $order->get_customer_id();

            if ($plugin_status === 'b2b') {
              $hours_sales_array[$hour] += $order_total;
            } else {
              // check user
              if (get_user_meta($order_user_id, 'b2bking_b2buser', true) === 'yes') {
                $hours_sales_array[$hour] += $order_total;
              } else {
                $hours_sales_b2c_array[$hour] += $order_total;
              }
            }
          }
        }

        array_push($days_sales_array, $sales_total);
        array_push($days_sales_b2c_array, $sales_total_b2c);
        $i++;
      }

      $data['days_sales_array'] = $days_sales_array;
      $data['days_sales_b2c_array'] = $days_sales_b2c_array;
      $data['hours_sales_array'] = $hours_sales_array;
      $data['hours_sales_b2c_array'] = $hours_sales_b2c_array;
      $data['total_b2b_sales_today'] = $total_b2b_sales_today;
      $data['total_b2b_sales_seven_days'] = $total_b2b_sales_seven_days;
      $data['total_b2b_sales_thirtyone_days'] = $total_b2b_sales_thirtyone_days;
      $data['number_b2b_sales_today'] = $number_b2b_sales_today;
      $data['number_b2b_sales_seven_days'] = $number_b2b_sales_seven_days;
      $data['number_b2b_sales_thirtyone_days'] = $number_b2b_sales_thirtyone_days;
      $data['customers_b2b_sales_today'] = $customers_b2b_sales_today;
      $data['customers_b2b_sales_seven_days'] = $customers_b2b_sales_seven_days;
      $data['customers_b2b_sales_thirtyone_days'] = $customers_b2b_sales_thirtyone_days;
      $data['tax_b2b_sales_today'] = $tax_b2b_sales_today;
      $data['tax_b2b_sales_seven_days'] = $tax_b2b_sales_seven_days;
      $data['tax_b2b_sales_thirtyone_days'] = $tax_b2b_sales_thirtyone_days;

      set_transient('webwizards_dashboard_data_cache', $data);
      set_transient('webwizards_dashboard_data_cache_time', time());
    }


    return $data;
  }

  private static function convert_to_base_currency($order, $value)
  {
    if (defined('WOOCS_VERSION')) {
      global $WOOCS;
      $order_currency = $order->get_currency();
      if ($order_currency && $order_currency != $WOOCS->default_currency) {
        $currencies = $WOOCS->get_currencies();
        if (isset($currencies[$order_currency]['rate']) && floatval($currencies[$order_currency]['rate']) > 0) {
          $value = floatval($value) / floatval($currencies[$order_currency]['rate']);
        }
      }
    }
    return $value;
  }

  public static function b2bking_reports_page_content()
  {

    // preloader if not in ajax - in ajax preloader is added via JS for smoother animations
    if (!wp_doing_ajax()) {
      ?>
      <div class="b2bkingpreloader">
        <img class="b2bking_loader_icon_button"
          src="<?php echo esc_attr(plugins_url('/assets/images/loaderpagegold5.svg', __FILE__)); ?>">
      </div>
      <?php
    }

    ?>
    <div id="b2bking_dashboard_wrapper">
      <div class="b2bking_dashboard_page_wrapper b2bking_reports_page_wrapper">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12">
              <div class="card card-hover">
                <div class="card-body">
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
                          <a hreflang="lastmonth"
                            class="b2bking_reports_link"><?php esc_html_e('Last Month', 'b2bking'); ?></a>
                          <a hreflang="thisyear"
                            class="b2bking_reports_link"><?php esc_html_e('This Year', 'b2bking'); ?></a>
                          <a hreflang="lastyear"
                            class="b2bking_reports_link"><?php esc_html_e('Last Year', 'b2bking'); ?></a>
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
                      src="<?php echo esc_attr(plugins_url('/assets/images/loaderpagegold5.svg', __FILE__)); ?>">
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
                <div class="card-body border-top">
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
                        <div class="m-r-10"><span class="text-cyan display-5"><i
                              class="mdi mdi-cart-outline"></i></i></span></div>
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
                        <div class="m-r-10"><span class="text-primary display-5"><i
                              class="mdi mdi-tag-multiple"></i></i></span></div>
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
                        <div class="m-r-10"><span class="text-cyan display-5"><i
                              class="mdi mdi-credit-card-off"></i></i></span></div>
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
                        <div class="m-r-10"><span class="text-primary display-5"><i
                              class="mdi mdi-truck-delivery"></i></i></span></div>
                        <div><span><?php esc_html_e('Shipping Charges', 'b2bking'); ?></span>
                          <h3 class="b2bking_reports_shipping_charges font-medium m-b-0"><?php echo 0; ?></h3>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
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
}
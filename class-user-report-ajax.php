<?php

namespace JPJULIAO\B2BKing\User_Dashboard;

class User_Report_AJAX
{
  public function __construct()
  {
    add_action('wp_ajax_b2bking_custom_data', [$this, 'ajax_handler']);
    add_action('wp_ajax_nopriv_b2bking_custom_data', [$this, 'ajax_handler']);
  }

  public function ajax_handler(): void
  {
    if (!check_ajax_referer('b2bking_security_nonce', 'security')) {
      wp_send_json_error('Invalid security token sent.');
      wp_die();
    }

    $this->print_data();
    wp_die();

  }

  public function print_data(): void
  {

    $customers = 'user_' . get_current_user_id();
    $firstday = sanitize_text_field($_POST['firstday']);
    $lastday = sanitize_text_field($_POST['lastday']);

    $timezone = get_option('timezone_string');
    if (empty($timezone) || $timezone === null) {
      $timezone = 'UTC';
    }
    date_default_timezone_set($timezone);

    $date_to = $lastday;
    $date_from = $firstday;

    $args = array(
      'status' => apply_filters('b2bking_reports_statuses', array('wc-on-hold', 'wc-pending', 'wc-processing', 'wc-completed')),
      'date_created' => $date_from . '...' . $date_to,
      'limit' => -1,
      'type' => 'shop_order',

    );

    $orders = \wc_get_orders($args);

    $args = array(
      'status' => array('wc-refunded'),
      'date_created' => $date_from . '...' . $date_to,
      'limit' => -1,
      'type' => 'shop_order',

    );
    $orders_refunded = \wc_get_orders($args);

    $group_explode = explode('_', $customers);
    if ($group_explode[0] === 'user') {
      // remove non-group orders
      foreach ($orders as $index => $order) {
        $order_customer = $order->get_customer_id();
        if (intval($order_customer) !== intval($group_explode[1])) {
          unset($orders[$index]);
        }
      }
      foreach ($orders_refunded as $index => $order) {
        $order_customer = $order->get_customer_id();
        if (intval($order_customer) !== intval($group_explode[1])) {
          unset($orders_refunded[$index]);
        }
      }
    }



    $timedifference = strtotime($lastday) - strtotime($firstday);
    $nrdays = intval(ceil($timedifference / 86400));

    //calculate sales total and order numbers
    $gross_sales = 0;
    $net_sales = 0;
    $order_number = 0;
    $items_purchased = 0;

    // average order value will be calculated later, gross orders total / number of days
    $refund_amount = 0;//fake
    $coupons_amount = 0; // fake
    $shipping_charges = 0;

    $timestamps_sales_gross = array();
    $timestamps_sales_net = array();
    $timestamps_nr_orders = array();
    $timestamps_nr_items = array();
    $timestamps_refund_amount = array();
    $timestamps_coupons_amount = array();
    $timestamps_shipping_charges = array();

    foreach ($orders_refunded as $order) {

      $orderobj = $order;
      if ($orderobj) {
        $date = $orderobj->get_date_created()->getTimestamp() + (get_option('gmt_offset') * 3600);
        $order_total = $orderobj->get_total();
        if (defined('WOOCS_VERSION')) {
          global $WOOCS;
          $order_currency = $orderobj->get_currency();
          if ($order_currency && $order_currency != $WOOCS->default_currency) {
            $currencies = $WOOCS->get_currencies();
            if (isset($currencies[$order_currency]['rate']) && floatval($currencies[$order_currency]['rate']) > 0) {
              $order_total = floatval($order_total) / floatval($currencies[$order_currency]['rate']);
            }
          }
        }

        $refund_amount += $order_total;
        $timestamps_refund_amount[$date] = $refund_amount;
      }

    }

    foreach ($orders as $order) {

      $orderobj = $order;

      if ($orderobj) {
        $order_total = $orderobj->get_total();
        $order_tax = $orderobj->get_total_tax();
        $order_shipping = floatval($orderobj->get_shipping_total());
        $rate = 1;
        if (defined('WOOCS_VERSION')) {
          global $WOOCS;
          $order_currency = $orderobj->get_currency();
          if ($order_currency && $order_currency != $WOOCS->default_currency) {
            $currencies = $WOOCS->get_currencies();
            if (isset($currencies[$order_currency]['rate']) && floatval($currencies[$order_currency]['rate']) > 0) {
              $rate = floatval($currencies[$order_currency]['rate']);
              $order_total = floatval($order_total) / $rate;
              $order_tax = floatval($order_tax) / $rate;
              $order_shipping = floatval($order_shipping) / $rate;
            }
          }
        }

        $gross_sales += $order_total;
        $net_sales = $net_sales + $order_total - $order_tax - $order_shipping;
        $order_number++;
        $items_purchased += $orderobj->get_item_count();


        // loop through order items "coupon"
        $coupons_amount_this_order = 0;
        foreach ($orderobj->get_items('coupon') as $item_id => $item) {
          $data = $item->get_data();
          $discount = $data['discount'];
          $discount_tax = $data['discount_tax'];
          if ($rate != 1) {
            $discount = floatval($discount) / $rate;
            $discount_tax = floatval($discount_tax) / $rate;
          }
          $coupons_amount += $discount + $discount_tax;
          $coupons_amount_this_order += $discount + $discount_tax;
        }

        $shipping_charges += $order_shipping;


        $date = $orderobj->get_date_created()->getTimestamp() + (get_option('gmt_offset') * 3600);
        $timestamps_sales_gross[$date] = $order_total;
        $timestamps_sales_net[$date] = ($order_total - $order_tax - $order_shipping);
        $timestamps_nr_orders[$date] = 1;
        $timestamps_nr_items[$date] = $orderobj->get_item_count();
        $timestamps_coupons_amount[$date] = $coupons_amount_this_order;
        $timestamps_shipping_charges[$date] = $order_shipping;
      }


    }


    $gross_sales_wc = wc_price($gross_sales);
    $net_sales_wc = wc_price($net_sales);
    // orders places INT
    // items purchases INT
    if ($order_number !== 0) {
      $average_order_value_wc = wc_price(round($gross_sales / $order_number, 2));
    } else {
      $average_order_value_wc = wc_price(0);
    }
    $refund_amount_wc = wc_price($refund_amount);
    $coupons_amount_wc = wc_price($coupons_amount);
    $shipping_charges_wc = wc_price($shipping_charges);


    // 1. Establish draw labels in chart
    /*
  if user chooses < 32 days, show by day ; if they choose > 31 < 366 show by month; > 366 show by year
    */

    if ($nrdays < 32) { // 32 days
      // show days
      $firstdaynumber = date('d', strtotime($firstday));

      $days_array = array();
      $gross_sales_array = array();
      $net_sales_array = array();
      $ordernr_array = array();

      $itemnr_array = array();
      $refund_array = array();
      $coupons_array = array();
      $shipping_array = array();

      $i = 0;
      while ($i <= $nrdays) {
        // build label
        array_push($days_array, date('d', (strtotime($firstday) + 86400 * $i)));

        // for each day, get sales, ordernr, commission
        $ordernr_of_the_day = 0;
        $gross_sales_of_the_day = 0;
        $net_sales_of_the_day = 0;

        $item_nr_of_the_day = 0;
        $refund_amount_of_the_day = 0;
        $coupon_amount_of_the_day = 0;
        $shipping_amount_of_the_day = 0;


        foreach ($timestamps_sales_gross as $timestamp => $sales) {
          if (date("m.d.y", $timestamp) === date("m.d.y", strtotime($firstday) + 86400 * $i)) {
            $gross_sales_of_the_day += $sales;
            $ordernr_of_the_day++;
          }
        }
        foreach ($timestamps_sales_net as $timestamp => $sales) {
          if (date("m.d.y", $timestamp) === date("m.d.y", strtotime($firstday) + 86400 * $i)) {
            $net_sales_of_the_day += $sales;
          }
        }
        foreach ($timestamps_nr_items as $timestamp => $sales) {
          if (date("m.d.y", $timestamp) === date("m.d.y", strtotime($firstday) + 86400 * $i)) {
            $item_nr_of_the_day += $sales;
          }
        }
        foreach ($timestamps_refund_amount as $timestamp => $sales) {
          if (date("m.d.y", $timestamp) === date("m.d.y", strtotime($firstday) + 86400 * $i)) {
            $refund_amount_of_the_day += $sales;
          }
        }
        foreach ($timestamps_coupons_amount as $timestamp => $sales) {
          if (date("m.d.y", $timestamp) === date("m.d.y", strtotime($firstday) + 86400 * $i)) {
            $coupon_amount_of_the_day += $sales;
          }
        }
        foreach ($timestamps_shipping_charges as $timestamp => $sales) {
          if (date("m.d.y", $timestamp) === date("m.d.y", strtotime($firstday) + 86400 * $i)) {
            $shipping_amount_of_the_day += $sales;
          }
        }

        array_push($gross_sales_array, $gross_sales_of_the_day);
        array_push($net_sales_array, $net_sales_of_the_day);
        array_push($ordernr_array, $ordernr_of_the_day);

        array_push($itemnr_array, $item_nr_of_the_day);
        array_push($refund_array, $refund_amount_of_the_day);
        array_push($coupons_array, $coupon_amount_of_the_day);
        array_push($shipping_array, $shipping_amount_of_the_day);

        $i++;

      }

      $labels = json_encode($days_array);

    } else if ($nrdays >= 32) {

      // show months
      $firstmonthnumber = date('m.y', strtotime($firstday));
      $lastmonthnumber = date('m.y', strtotime($lastday));

      $months_array = array();
      $gross_sales_array = array();
      $net_sales_array = array();
      $ordernr_array = array();

      $itemnr_array = array();
      $refund_array = array();
      $coupons_array = array();
      $shipping_array = array();

      $i = 1;
      while ($i !== 'stop') {

        // for each month, get sales, ordernr, commission
        $gross_sales_of_the_month = 0;
        $net_sales_of_the_month = 0;
        $ordernr_of_the_month = 0;

        $item_nr_of_the_month = 0;
        $refund_amount_of_the_month = 0;
        $coupon_amount_of_the_month = 0;
        $shipping_amount_of_the_month = 0;

        foreach ($timestamps_sales_gross as $timestamp => $sales) {
          if (date("m.y", $timestamp) === $firstmonthnumber) {
            $gross_sales_of_the_month += $sales;
            $ordernr_of_the_month++;
          }
        }
        foreach ($timestamps_sales_net as $timestamp => $sales) {
          if (date("m.y", $timestamp) === $firstmonthnumber) {
            $net_sales_of_the_month += $sales;
          }
        }
        foreach ($timestamps_nr_items as $timestamp => $sales) {
          if (date("m.y", $timestamp) === $firstmonthnumber) {
            $item_nr_of_the_month += $sales;
          }
        }
        foreach ($timestamps_refund_amount as $timestamp => $sales) {
          if (date("m.y", $timestamp) === $firstmonthnumber) {
            $refund_amount_of_the_month += $sales;
          }
        }
        foreach ($timestamps_coupons_amount as $timestamp => $sales) {
          if (date("m.y", $timestamp) === $firstmonthnumber) {
            $coupon_amount_of_the_month += $sales;
          }
        }
        foreach ($timestamps_shipping_charges as $timestamp => $sales) {
          if (date("m.y", $timestamp) === $firstmonthnumber) {
            $shipping_amount_of_the_month += $sales;
          }
        }

        array_push($gross_sales_array, $gross_sales_of_the_month);
        array_push($net_sales_array, $net_sales_of_the_month);
        array_push($ordernr_array, $ordernr_of_the_month);

        array_push($itemnr_array, $item_nr_of_the_month);
        array_push($refund_array, $refund_amount_of_the_month);
        array_push($coupons_array, $coupon_amount_of_the_month);
        array_push($shipping_array, $shipping_amount_of_the_month);


        // build label
        array_push($months_array, date("M y", strtotime("+" . ($i - 1) . " month", strtotime($firstday))));

        if ($firstmonthnumber === $lastmonthnumber) {
          $i = 'stop';
        } else {
          $firstmonthnumber = date("m.y", strtotime("+" . $i . " month", strtotime($firstday)));
          $i++;
        }
      }

      $labels = json_encode($months_array);

    }

    // round values to 2 decimals
    foreach ($gross_sales_array as $index => $value) {
      $gross_sales_array[$index] = round($value, 2);
    }
    foreach ($net_sales_array as $index => $value) {
      $net_sales_array[$index] = round($value, 2);
    }
    foreach ($refund_array as $index => $value) {
      $refund_array[$index] = round($value, 2);
    }
    foreach ($coupons_array as $index => $value) {
      $coupons_array[$index] = round($value, 2);
    }
    foreach ($shipping_array as $index => $value) {
      $shipping_array[$index] = round($value, 2);
    }


    $grosssalestotal = json_encode($gross_sales_array);
    $netsalestotal = json_encode($net_sales_array);
    $ordernumbers = json_encode($ordernr_array);

    $itemnrtotal = json_encode($itemnr_array);
    $refundtotal = json_encode($refund_array);
    $coupontotal = json_encode($coupons_array);
    $shippingtotal = json_encode($shipping_array);


    echo $labels . '*' . $grosssalestotal . '*' . $netsalestotal . '*' . $ordernumbers . '*' . $gross_sales_wc . '*' . $net_sales_wc . '*' . $order_number . '*' . $items_purchased . '*' . $average_order_value_wc . '*' . $refund_amount_wc . '*' . $coupons_amount_wc . '*' . $shipping_charges_wc . '*' . $itemnrtotal . '*' . $refundtotal . '*' . $coupontotal . '*' . $shippingtotal;

    exit();
  }

}
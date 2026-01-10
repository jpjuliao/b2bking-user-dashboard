<?php

/**
 * Plugin Name: B2BKing Addons
 * Plugin URI: https://jpjuliao.github.io
 * Description: This plugin extends B2BKing plugin to add an advanced report section in the user account and an information table without prices for guest.
 * Version: 1.0.0
 * Author: Juan Pablo Juliao
 * Author URI: https://jpjuliao.github.io
 * Text Domain: jpjuliao-b2bking-user-dashboard
 * Requires Plugins: b2bking, b2bking-wholesale-for-woocommerce, woocommerce
 */

namespace JPJULIAO\B2BKing\User_Dashboard;

if (!defined('WPINC')) {
  die;
}

function is_b2b_user(): bool
{
  $user_id = get_current_user_id();
  $is_b2b = get_user_meta($user_id, 'b2bking_b2buser', true);
  return $is_b2b === 'yes';
}

add_action('template_redirect', function () {
  if (is_b2b_user()) {
    require_once plugin_dir_path(__FILE__) . 'class-user-report-scripts.php';
    new User_Report_Scripts();
    require_once plugin_dir_path(__FILE__) . 'class-user-report.php';
    new User_Report();
  } else {
    require_once plugin_dir_path(__FILE__) . 'class-guest-info-table.php';
    new Guest_Info_Table();
  }
});

add_action('wp_loaded', function () {
  if (!is_b2b_user() || !wp_doing_ajax()) {
    return;
  }
  require_once plugin_dir_path(__FILE__) . 'class-user-report-ajax.php';
  new User_Report_AJAX();
});

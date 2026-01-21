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

namespace JPJULIAO\B2BKing_Addons;

if (!defined('WPINC')) {
  die;
}

define('PLUGIN_PATH', plugin_dir_path(__FILE__));
define('PLUGIN_URL', plugin_dir_url(__FILE__));

function is_b2b_user(): bool
{
  $user_id = get_current_user_id();
  $is_b2b = get_user_meta($user_id, 'b2bking_b2buser', true);
  return $is_b2b === 'yes';
}

function get_modules(): array
{
  return [
    'User_Report' => [
      'filename' => 'user-report/class-user-report',
      'enabled' => true,
    ],
    'Guest_Info_Table' => [
      'filename' => 'guest-info-table/class-guest-info-table',
      'enabled' => true,
    ],
    'B2B_Info_Table' => [
      'filename' => 'b2b-info-table/class-b2b-info-table',
      'enabled' => true,
    ],
    'Variations_Columns' => [
      'filename' => 'variations-columns/class-variations-columns',
      'enabled' => true,
    ],
    'Shop_Filters' => [
      'filename' => 'shop-filters/class-shop-filters',
      'enabled' => true,
    ],
    'Bulk_Actions' => [
      'filename' => 'bulk-actions/class-bulk-actions',
      'enabled' => false,
    ],
    'Product_Lab_Reports' => [
      'filename' => 'product-lab-reports/class-product-lab-reports',
      'enabled' => true,
    ],
  ];
}

function init_modules(array $modules): void
{
  foreach ($modules as $class_name => $module) {
    if (!$module['enabled']) {
      continue;
    }

    require_once(
      PLUGIN_PATH . "modules/{$module['filename']}.php"
    );

    $class_name = __NAMESPACE__ . '\\' . $class_name;

    new $class_name();
  }
}

init_modules(get_modules());
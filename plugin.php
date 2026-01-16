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

function is_b2b_user(): bool
{
  $user_id = get_current_user_id();
  $is_b2b = get_user_meta($user_id, 'b2bking_b2buser', true);
  return $is_b2b === 'yes';
}

define('PLUGIN_PATH', plugin_dir_path(__FILE__));
define('PLUGIN_URL', plugin_dir_url(__FILE__));

require_once(
  PLUGIN_PATH . 'modules/user-report/class-user-report.php'
);
require_once(
  PLUGIN_PATH . 'modules/guest-info-table/class-guest-info-table.php'
);
require_once(
  PLUGIN_PATH . 'modules/b2b-info-table/class-b2b-info-table.php'
);
require_once(
  PLUGIN_PATH . 'modules/variations-columns/class-variations-columns.php'
);
require_once(
  PLUGIN_PATH . 'modules/shop-filters/class-shop-filters.php'
);
// require_once(
//   PLUGIN_PATH . 'modules/bulk-actions/class-bulk-actions.php'
// );

new User_Report();
new Guest_Info_Table();
new B2B_Info_Table();
new Variations_Columns();
new Shop_Filters();

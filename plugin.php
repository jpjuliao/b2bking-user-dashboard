<?php

/**
 * Plugin Name: B2BKing User Dashboard
 * Plugin URI: https://jpjuliao.github.io
 * Description: User Dashboard for B2BKing
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

function check_plugin_dependencies()
{
  if (!function_exists('is_plugin_active')) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
  }

  $plugin_slug = [
    'b2bking/b2bking.php',
    'b2bking-wholesale-for-woocommerce/b2bking.php',
    'woocommerce/woocommerce.php'
  ];

  $bypass = true;

  foreach ($plugin_slug as $slug) {
    $is_installed = array_key_exists($slug, get_plugins());
    $is_active = is_plugin_active($slug);

    if (!$is_installed || !$is_active) {
      add_action('admin_notices', function () use ($slug) {
        ?>
        <div class="notice notice-warning">
          <p>
            <?php _e(
              'B2BKing User Dashboard requires B2BKing Pro to be ' .
              ' active. Please activate ' . $slug . ' to use this plugin.',
              'jpjuliao-b2bking-user-dashboard'
            ); ?>
          </p>
        </div>
        <?php
      });
      $bypass = false;
    }
  }

  return $bypass;
}

if (!check_plugin_dependencies()) {
  return;
}

add_action('wp_loaded', function () {
  $user_id = get_current_user_id();
  $is_b2b = get_user_meta($user_id, 'b2bking_b2buser', true);
  if ($is_b2b !== 'yes') {
    return;
  }
  require_once plugin_dir_path(__FILE__) . 'class-ui.php';
  require_once plugin_dir_path(__FILE__) . 'class-scripts.php';
  require_once plugin_dir_path(__FILE__) . 'class-ajax.php';
  new UI();
  new Scripts();
  new AJAX();
});
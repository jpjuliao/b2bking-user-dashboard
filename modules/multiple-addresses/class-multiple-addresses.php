<?php

namespace JPJULIAO\B2BKing_Addons;

if (!defined('ABSPATH')) {
  exit;
}

class Multiple_Addresses
{

  public function __construct()
  {
    add_action('init', array(
      $this,
      'init'
    ));
    add_action('woocommerce_account_menu_items', array(
      $this,
      'add_account_menu_item'
    ));
    add_action('woocommerce_account_addresses_endpoint', array(
      $this,
      'addresses_endpoint_content'
    ));
    add_filter('query_vars', array(
      $this,
      'add_query_vars'
    ));
    add_action('wp_ajax_wc_save_address', array(
      $this,
      'ajax_save_address'
    ));
    add_action('wp_ajax_wc_delete_address', array(
      $this,
      'ajax_delete_address'
    ));
    add_action('wp_ajax_wc_set_default_address', array(
      $this,
      'ajax_set_default_address'
    ));
    add_action('woocommerce_before_checkout_shipping_form', array(
      $this,
      'checkout_address_selector'
    ));
    add_action('woocommerce_checkout_update_order_meta', array(
      $this,
      'save_checkout_address'
    ));
    add_action('wp_enqueue_scripts', array(
      $this,
      'enqueue_scripts'
    ));
  }

  public function init(): void
  {
    add_rewrite_endpoint('addresses', EP_ROOT | EP_PAGES);
    flush_rewrite_rules();
  }

  public function add_query_vars(array $vars): array
  {
    $vars[] = 'addresses';
    return $vars;
  }

  public function add_account_menu_item(array $items): array
  {
    $new_items = array();
    foreach ($items as $key => $value) {
      if ($key === 'edit-address') {
        $new_items['addresses'] = __('Address Book', 'woocommerce');
      } else {
        $new_items[$key] = $value;
      }
    }
    return $new_items;
  }

  public function enqueue_scripts(): void
  {
    if (is_checkout() || is_account_page()) {
      wp_enqueue_script('wc-multiple-addresses', plugin_dir_url(__FILE__) . 'assets/js/script.js', array('jquery'), '1.0.0', true);
      wp_localize_script('wc-multiple-addresses', 'wcMultipleAddresses', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('wc_multiple_addresses')
      ));
    }
  }

  public function get_user_addresses(int $user_id): array
  {
    $addresses = get_user_meta($user_id, '_wc_multiple_addresses', true);
    $addresses = $addresses ? $addresses : array();

    $core_address = array(
      'first_name' => get_user_meta($user_id, 'shipping_first_name', true),
      'last_name' => get_user_meta($user_id, 'shipping_last_name', true),
      'company' => get_user_meta($user_id, 'shipping_company', true),
      'address_1' => get_user_meta($user_id, 'shipping_address_1', true),
      'address_2' => get_user_meta($user_id, 'shipping_address_2', true),
      'city' => get_user_meta($user_id, 'shipping_city', true),
      'state' => get_user_meta($user_id, 'shipping_state', true),
      'postcode' => get_user_meta($user_id, 'shipping_postcode', true),
      'country' => get_user_meta($user_id, 'shipping_country', true),
      'phone' => get_user_meta($user_id, 'shipping_phone', true),
    );

    if (array_filter($core_address)) {
      $addresses['wc_core_shipping'] = $core_address;
    }

    return $addresses;
  }

  public function save_user_addresses(int $user_id, array $addresses): void
  {
    if (isset($addresses['wc_core_shipping'])) {
      $core = $addresses['wc_core_shipping'];
      $fields = array('first_name', 'last_name', 'company', 'address_1', 'address_2', 'city', 'state', 'postcode', 'country', 'phone');

      foreach ($fields as $field) {
        if (isset($core[$field])) {
          update_user_meta($user_id, 'shipping_' . $field, $core[$field]);
        }
      }

      unset($addresses['wc_core_shipping']);
    }

    update_user_meta($user_id, '_wc_multiple_addresses', $addresses);
  }

  public function addresses_endpoint_content(): void
  {
    $user_id = get_current_user_id();
    $addresses = $this->get_user_addresses($user_id);
    $default_address_id = get_user_meta($user_id, '_wc_default_address_id', true);

    ?>
    <div class="woocommerce-addresses">
      <h3>
        <?php _e('Manage Your Addresses', 'woocommerce'); ?>
      </h3>

      <button id="add-new-address" class="button">
        <?php _e('Add New Address', 'woocommerce'); ?>
      </button>

      <div id="address-form" style="display:none; margin-top: 20px; padding: 20px; border: 1px solid #ddd;">
        <h4>
          <?php _e('Address Details', 'woocommerce'); ?>
        </h4>
        <form id="save-address-form">
          <input type="hidden" name="address_id" id="address_id" value="">
          <p>
            <label>
              <?php _e('First Name', 'woocommerce'); ?> *
            </label>
            <input type="text" name="first_name" id="first_name" required>
          </p>
          <p>
            <label>
              <?php _e('Last Name', 'woocommerce'); ?> *
            </label>
            <input type="text" name="last_name" id="last_name" required>
          </p>
          <p>
            <label>
              <?php _e('Company', 'woocommerce'); ?>
            </label>
            <input type="text" name="company" id="company">
          </p>
          <p>
            <label>
              <?php _e('Address Line 1', 'woocommerce'); ?> *
            </label>
            <input type="text" name="address_1" id="address_1" required>
          </p>
          <p>
            <label>
              <?php _e('Address Line 2', 'woocommerce'); ?>
            </label>
            <input type="text" name="address_2" id="address_2">
          </p>
          <p>
            <label>
              <?php _e('City', 'woocommerce'); ?> *
            </label>
            <input type="text" name="city" id="city" required>
          </p>
          <p>
            <label>
              <?php _e('State / County', 'woocommerce'); ?> *
            </label>
            <input type="text" name="state" id="state" required>
          </p>
          <p>
            <label>
              <?php _e('Postcode / ZIP', 'woocommerce'); ?> *
            </label>
            <input type="text" name="postcode" id="postcode" required>
          </p>
          <p>
            <label>
              <?php _e('Country', 'woocommerce'); ?> *
            </label>
            <input type="text" name="country" id="country" required>
          </p>
          <p>
            <label>
              <?php _e('Phone', 'woocommerce'); ?>
            </label>
            <input type="text" name="phone" id="phone">
          </p>
          <p>
            <button type="submit" class="button">
              <?php _e('Save Address', 'woocommerce'); ?>
            </button>
            <button type="button" id="cancel-address" class="button">
              <?php _e('Cancel', 'woocommerce'); ?>
            </button>
          </p>
        </form>
      </div>

      <div id="addresses-list" style="margin-top: 30px;">
        <?php if (empty($addresses)): ?>
          <p>
            <?php _e('No addresses saved yet.', 'woocommerce'); ?>
          </p>
        <?php else: ?>
          <?php foreach ($addresses as $id => $address): ?>
            <div class="address-item" style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd;">
              <?php if ($default_address_id == $id): ?>
                <span style="background: #0073aa; color: white; padding: 2px 8px; font-size: 12px;">
                  <?php _e('Default', 'woocommerce'); ?>
                </span>
              <?php endif; ?>
              <p><strong>
                  <?php echo esc_html($address['first_name'] . ' ' . $address['last_name']); ?>
                </strong></p>
              <?php if (!empty($address['company'])): ?>
                <p>
                  <?php echo esc_html($address['company']); ?>
                </p>
              <?php endif; ?>
              <p>
                <?php echo esc_html($address['address_1']); ?>
              </p>
              <?php if (!empty($address['address_2'])): ?>
                <p>
                  <?php echo esc_html($address['address_2']); ?>
                </p>
              <?php endif; ?>
              <p>
                <?php echo esc_html($address['city'] . ', ' . $address['state'] . ' ' . $address['postcode']); ?>
              </p>
              <p>
                <?php echo esc_html($address['country']); ?>
              </p>
              <?php if (!empty($address['phone'])): ?>
                <p>
                  <?php _e('Phone:', 'woocommerce'); ?>
                  <?php echo esc_html($address['phone']); ?>
                </p>
              <?php endif; ?>
              <p>
                <button class="button edit-address" data-id="<?php echo esc_attr($id); ?>">
                  <?php _e('Edit', 'woocommerce'); ?>
                </button>
                <button class="button delete-address" data-id="<?php echo esc_attr($id); ?>">
                  <?php _e('Delete', 'woocommerce'); ?>
                </button>
                <?php if ($default_address_id != $id): ?>
                  <button class="button set-default-address" data-id="<?php echo esc_attr($id); ?>">
                    <?php _e('Set as Default', 'woocommerce'); ?>
                  </button>
                <?php endif; ?>
              </p>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
    <?php
  }

  public function ajax_save_address(): void
  {
    check_ajax_referer('wc_multiple_addresses', 'nonce');

    $user_id = get_current_user_id();
    if (!$user_id) {
      wp_send_json_error('Not logged in');
    }

    $addresses = $this->get_user_addresses($user_id);
    $address_id = sanitize_text_field($_POST['address_id']);

    if (empty($address_id)) {
      $address_id = uniqid('addr_');
    }

    $addresses[$address_id] = array(
      'first_name' => sanitize_text_field($_POST['first_name']),
      'last_name' => sanitize_text_field($_POST['last_name']),
      'company' => sanitize_text_field($_POST['company']),
      'address_1' => sanitize_text_field($_POST['address_1']),
      'address_2' => sanitize_text_field($_POST['address_2']),
      'city' => sanitize_text_field($_POST['city']),
      'state' => sanitize_text_field($_POST['state']),
      'postcode' => sanitize_text_field($_POST['postcode']),
      'country' => sanitize_text_field($_POST['country']),
      'phone' => sanitize_text_field($_POST['phone'])
    );

    $this->save_user_addresses($user_id, $addresses);

    if (empty(get_user_meta($user_id, '_wc_default_address_id', true))) {
      update_user_meta($user_id, '_wc_default_address_id', $address_id);
    }

    wp_send_json_success();
  }

  public function ajax_delete_address(): void
  {
    check_ajax_referer('wc_multiple_addresses', 'nonce');

    $user_id = get_current_user_id();
    if (!$user_id) {
      wp_send_json_error('Not logged in');
    }

    $addresses = $this->get_user_addresses($user_id);
    $address_id = sanitize_text_field($_POST['address_id']);

    if (isset($addresses[$address_id])) {
      if ($address_id === 'wc_core_shipping') {
        $fields = array('first_name', 'last_name', 'company', 'address_1', 'address_2', 'city', 'state', 'postcode', 'country', 'phone');
        foreach ($fields as $field) {
          update_user_meta($user_id, 'shipping_' . $field, '');
        }
        unset($addresses[$address_id]);
      } else {
        unset($addresses[$address_id]);
      }
      $this->save_user_addresses($user_id, $addresses);

      $default_id = get_user_meta($user_id, '_wc_default_address_id', true);
      if ($default_id == $address_id) {
        delete_user_meta($user_id, '_wc_default_address_id');
        if (!empty($addresses)) {
          $first_key = array_key_first($addresses);
          update_user_meta($user_id, '_wc_default_address_id', $first_key);
        }
      }
    }

    wp_send_json_success();
  }

  public function ajax_set_default_address(): void
  {
    check_ajax_referer('wc_multiple_addresses', 'nonce');

    $user_id = get_current_user_id();
    if (!$user_id) {
      wp_send_json_error('Not logged in');
    }

    $address_id = sanitize_text_field($_POST['address_id']);
    update_user_meta($user_id, '_wc_default_address_id', $address_id);

    wp_send_json_success();
  }

  public function checkout_address_selector($checkout): void
  {
    if (!is_user_logged_in()) {
      return;
    }

    $user_id = get_current_user_id();
    $addresses = $this->get_user_addresses($user_id);
    $default_address_id = get_user_meta($user_id, '_wc_default_address_id', true);

    if (empty($addresses)) {
      return;
    }

    ?>
    <div class="woocommerce-shipping-addresses">
      <h3>
        <?php _e('Select Shipping Address', 'woocommerce'); ?>
      </h3>
      <select name="selected_address_id" id="selected_address_id">
        <option value="">
          <?php _e('Enter new address', 'woocommerce'); ?>
        </option>
        <?php foreach ($addresses as $id => $address): ?>
          <option value="<?php echo esc_attr($id); ?>" <?php selected($default_address_id, $id); ?>>
            <?php echo esc_html($address['first_name'] . ' ' . $address['last_name'] . ' - ' . $address['address_1'] . ', ' . $address['city']); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <?php
  }

  public function save_checkout_address(int $order_id): void
  {
    if (isset($_POST['selected_address_id']) && !empty($_POST['selected_address_id'])) {
      update_post_meta($order_id, '_selected_address_id', sanitize_text_field($_POST['selected_address_id']));
    }
  }
}
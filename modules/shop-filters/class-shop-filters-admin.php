<?php

namespace JPJULIAO\B2BKing_Addons;

class Shop_Filters_Admin
{
  const OPTION_NAME = 'b2bking_addons_shop_filters_settings';

  public function __construct()
  {
    add_action('admin_menu', [$this, 'add_admin_menu']);
    add_action('admin_init', [$this, 'register_settings']);
  }

  public function add_admin_menu()
  {
    add_submenu_page(
      'woocommerce',
      'Shop Filters',
      'Shop Filters',
      'manage_options',
      'b2bking-addons-shop-filters',
      [$this, 'settings_page_html']
    );
  }

  public function register_settings()
  {
    register_setting(
      'b2bking_addons_shop_filters_group',
      self::OPTION_NAME
    );

    add_settings_section(
      'b2bking_addons_shop_filters_section',
      'Filter Settings',
      '__return_empty_string',
      'b2bking-addons-shop-filters'
    );

    $fields = [
      'price' => 'Price',
      'product_cat' => 'Product Category',
      'product_tag' => 'Product Tag',
      'product_brand' => 'Product Brand',
      'new' => 'New Products',
      'discounts' => 'Discounts',
      'best_sellers' => 'Best Sellers',
      'attributes' => 'Attributes',
    ];

    foreach ($fields as $key => $label) {
      add_settings_field(
        $key,
        $label,
        [$this, 'render_field'],
        'b2bking-addons-shop-filters',
        'b2bking_addons_shop_filters_section',
        ['key' => $key, 'label' => $label]
      );
    }
  }

  public function render_field($args)
  {
    $options = get_option(self::OPTION_NAME);
    $key = $args['key'];

    if ($options === false) {
      $enabled = 1;
    } else {
      $enabled = isset($options[$key]['enabled']) ? $options[$key]['enabled'] : 0;
    }
    $title = isset($options[$key]['title']) ? $options[$key]['title'] : '';
    ?>
    <div style="margin-bottom: 10px;">
      <label>
        <input type="checkbox" name="<?php echo self::OPTION_NAME; ?>[<?php echo $key; ?>][enabled]" value="1" <?php checked($enabled, 1); ?>>
        Enable
      </label>
    </div>
    <div>
      <label>
        Custom Title:
        <input type="text" name="<?php echo self::OPTION_NAME; ?>[<?php echo $key; ?>][title]"
          value="<?php echo esc_attr($title); ?>" class="regular-text">
      </label>
    </div>
    <?php
  }

  public function settings_page_html()
  {
    if (!current_user_can('manage_options')) {
      return;
    }
    ?>
    <div class="wrap">
      <h1>
        <?php echo esc_html(get_admin_page_title()); ?>
      </h1>
      <form action="options.php" method="post">
        <?php
        settings_fields('b2bking_addons_shop_filters_group');
        do_settings_sections('b2bking-addons-shop-filters');
        submit_button('Save Settings');
        ?>
      </form>
    </div>
    <?php
  }
}

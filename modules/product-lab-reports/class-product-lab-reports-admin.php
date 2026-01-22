<?php

namespace JPJULIAO\B2BKing_Addons;

class Lab_Reports_Admin
{
  private static $instance = null;

  private function __construct()
  {
    add_action('admin_menu', [$this, 'add_admin_menu']);
  }

  public static function get_instance()
  {
    if (self::$instance === null) {
      self::$instance = new self();
    }
    return self::$instance;
  }

  public function add_admin_menu()
  {
    add_submenu_page(
      'woocommerce',
      'Lab Reports',
      'Lab Reports',
      'manage_woocommerce',
      'lab-reports',
      [$this, 'render_admin_page']
    );
  }

  public function render_admin_page()
  {
    $args = [
      'type' => 'variable',
      'status' => 'publish',
      'limit' => -1,
    ];

    $products = wc_get_products($args);
    $variations_data = [];

    foreach ($products as $product) {
      foreach ($product->get_available_variations() as $variation_data) {
        $variation_id = $variation_data['variation_id'];
        $lab_report_id = get_post_meta($variation_id, '_lab_report', true);
        $date_tested = get_post_meta($variation_id, '_lab_report_date', true);

        $attributes = [];
        foreach ($variation_data['attributes'] as $key => $value) {
          $attribute_name = str_replace('attribute_', '', $key);
          $attribute_label = wc_attribute_label($attribute_name, $product);
          $term = get_term_by('slug', $value, $attribute_name);
          $attribute_value = $term ? $term->name : $value;
          $attributes[] = $attribute_label . ': ' . $attribute_value;
        }

        $variations_data[] = [
          'product_id' => $product->get_id(),
          'product_name' => $product->get_name(),
          'variation_id' => $variation_id,
          'variation_name' => implode(', ', $attributes),
          'lab_report_id' => $lab_report_id,
          'date_tested' => $date_tested,
        ];
      }
    }

    ?>
    <div class="wrap">
      <h1>Lab Reports</h1>
      <table class="wp-list-table widefat fixed striped">
        <thead>
          <tr>
            <th>Product</th>
            <th>Variation</th>
            <th>File Name</th>
            <th>Date Tested</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($variations_data)): ?>
            <tr>
              <td colspan="5">No product variations found.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($variations_data as $data): ?>
              <?php
              $file_url = $data['lab_report_id'] ? wp_get_attachment_url($data['lab_report_id']) : '';
              $file_name = $file_url ? basename($file_url) : 'N/A';
              ?>
              <tr>
                <td>
                  <a href="<?php echo get_edit_post_link($data['product_id']); ?>">
                    <?php echo esc_html($data['product_name']); ?>
                  </a>
                </td>
                <td><?php echo esc_html($data['variation_name']); ?></td>
                <td><?php echo esc_html($file_name); ?></td>
                <td><?php echo esc_html($data['date_tested'] ?: 'N/A'); ?></td>
                <td>
                  <?php if ($file_url): ?>
                    <a href="<?php echo esc_url($file_url); ?>" class="button button-small" download>Download</a>
                  <?php endif; ?>
                  <a href="<?php echo get_edit_post_link($data['product_id']); ?>" class="button button-small">Edit Product</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <?php
  }
}
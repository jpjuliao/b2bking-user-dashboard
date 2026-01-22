<?php

namespace JPJULIAO\B2BKing_Addons;

if (!defined('ABSPATH')) {
  exit;
}

class Product_Lab_Reports
{

  public function __construct()
  {
    require_once plugin_dir_path(__FILE__) . 'class-product-lab-reports-admin.php';
    Lab_Reports_Admin::get_instance();

    add_action('woocommerce_product_after_variable_attributes', [
      $this,
      'add_variation_field'
    ], 10, 3);
    add_action('woocommerce_save_product_variation', [
      $this,
      'save_variation_field'
    ], 10, 2);
    add_action('admin_enqueue_scripts', [
      $this,
      'enqueue_admin_scripts'
    ]);
    add_shortcode('lab_reports_table', [
      $this,
      'render_table'
    ]);
  }

  public function enqueue_admin_scripts($hook)
  {
    if ('post.php' !== $hook && 'post-new.php' !== $hook) {
      return;
    }

    wp_enqueue_media();
    wp_enqueue_script(
      'lab-reports-admin',
      plugin_dir_url(__FILE__) . 'assets/js/admin.js',
      ['jquery'],
      '1.0.0',
      true
    );
  }

  public function add_variation_field($loop, $variation_data, $variation)
  {
    $attachment_id = get_post_meta($variation->ID, '_lab_report', true);
    $date_tested = get_post_meta($variation->ID, '_lab_report_date', true);
    $file_url = $attachment_id ? wp_get_attachment_url($attachment_id) : '';
    $file_name = $attachment_id ? basename($file_url) : '';

    ?>
    <div class="form-row form-row-full">
      <p class="form-field">
        <label><?php echo esc_html__('Lab Report', 'woocommerce'); ?></label>
        <input type="hidden" name="lab_report[<?php echo esc_attr($loop); ?>]" class="lab-report-id"
          id="lab-report-id-<?php echo esc_attr($loop); ?>" value="<?php echo esc_attr($attachment_id); ?>" />
        <button type="button" class="button upload-lab-report" data-loop="<?php echo esc_attr($loop); ?>"
          data-variation-id="<?php echo esc_attr($variation->ID); ?>">
          <?php echo $attachment_id ? esc_html__('Change PDF', 'woocommerce') : esc_html__('Upload PDF', 'woocommerce'); ?>
        </button>
        <button type="button" class="button remove-lab-report" data-loop="<?php echo esc_attr($loop); ?>"
          data-variation-id="<?php echo esc_attr($variation->ID); ?>"
          style="<?php echo $attachment_id ? '' : 'display:none;'; ?>">
          <?php echo esc_html__('Remove', 'woocommerce'); ?>
        </button>
        <span class="lab-report-filename"
          id="lab-report-filename-<?php echo esc_attr($loop); ?>"><?php echo esc_html($file_name); ?></span>
      </p>
      <p class="form-field">
        <label><?php echo esc_html__('Date Tested', 'woocommerce'); ?></label>
        <input type="date" name="lab_report_date[<?php echo esc_attr($loop); ?>]"
          id="lab-report-date-<?php echo esc_attr($loop); ?>" value="<?php echo esc_attr($date_tested); ?>" />
      </p>
    </div>
    <?php
  }

  public function save_variation_field($variation_id, $loop)
  {
    if (isset($_POST['lab_report'][$loop])) {
      $attachment_id = sanitize_text_field($_POST['lab_report'][$loop]);
      if (!empty($attachment_id)) {
        update_post_meta($variation_id, '_lab_report', $attachment_id);
      } else {
        delete_post_meta($variation_id, '_lab_report');
      }
    }

    if (isset($_POST['lab_report_date'][$loop])) {
      $date = sanitize_text_field($_POST['lab_report_date'][$loop]);
      if (!empty($date)) {
        update_post_meta($variation_id, '_lab_report_date', $date);
      } else {
        delete_post_meta($variation_id, '_lab_report_date');
      }
    }
  }

  public function render_table($atts)
  {
    $atts = shortcode_atts(['product_id' => get_the_ID()], $atts);
    $product = wc_get_product($atts['product_id']);

    if (!$product || !$product->is_type('variable')) {
      return '';
    }

    $variations = $product->get_available_variations();
    $rows = [];

    foreach ($variations as $variation) {
      $attachment_id = get_post_meta(
        $variation['variation_id'],
        '_lab_report',
        true
      );

      $file_url = $attachment_id
        ? wp_get_attachment_url($attachment_id)
        : false;

      $attributes = [];

      foreach ($variation['attributes'] as $key => $value) {
        $attribute_name = str_replace('attribute_', '', $key);
        $attribute_label = wc_attribute_label($attribute_name, $product);

        $term = get_term_by('slug', $value, $attribute_name);
        $attribute_value = $term ? $term->name : $value;

        $attributes[] = $attribute_label . ': ' . $attribute_value;
      }

      $variation_name = implode(', ', $attributes);

      $rows[] = sprintf(
        '<tr><td>%s</td><td>%s</td></tr>',
        esc_html($variation_name),
        $file_url
        ? '<a href="' . $file_url . '" class="button">'
        . esc_html__('Download', 'woocommerce') . '</a>'
        : esc_html__('N/A', 'woocommerce')
      );
    }

    if (empty($rows)) {
      return '';
    }

    $table = '<table class="lab-reports-table">';
    $table .= '<thead><tr>';
    $table .= '<th>' . esc_html__('Product', 'woocommerce') . '</th>';
    $table .= '<th>' . esc_html__('Download', 'woocommerce') . '</th>';
    $table .= '</tr></thead>';
    $table .= '<tbody>' . implode('', $rows) . '</tbody>';
    $table .= '</table>';

    return $table;
  }
}

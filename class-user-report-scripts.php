<?php
namespace JPJULIAO\B2BKing\User_Dashboard;

class User_Report_Scripts
{
  public $version = '1.0';

  public function __construct()
  {
    add_action('wp_enqueue_scripts', function () {
      $this->base_scripts();
      $this->main_scripts();
    });
  }

  public function main_scripts(): void
  {
    wp_enqueue_style(
      'b2bking_custom_style',
      plugins_url('./assets/css/adminglobal.css', __FILE__),
      $deps = array(),
      $ver = $this->version
    );

    wp_enqueue_style(
      'b2bking_custom_main_style',
      plugins_url('./assets/css/main.css', __FILE__),
      $deps = array(),
      $ver = $this->version
    );

    // wp_enqueue_style('wp-color-picker');
    wp_enqueue_script(
      'b2bking_custom_script',
      plugins_url('./assets/js/adminglobal.js', __FILE__),
      // $deps = array('wp-color-picker'),
      $deps = array(),
      $ver = $this->version,
      $in_footer = true
    );

    wp_localize_script(
      'b2bking_custom_script',
      'b2bking',
      $this->get_b2bking_data()
    );

    wp_localize_script(
      'b2bking_custom_script',
      'b2bking_dashboard',
      $this->get_dashboard_data()
    );

    wp_localize_script(
      'b2bking_custom_script',
      'ajaxurl',
      array(
        'security' => wp_create_nonce('b2bking_security_nonce'),
        'ajaxurl' => admin_url('admin-ajax.php'),
      ),
    );

  }

  public function get_b2bking_data(): array
  {
    if (defined('WC_PLUGIN_FILE')) {
      $symbol = get_woocommerce_currency_symbol();
      $offerslink = apply_filters('b2bking_offers_link', rtrim(get_permalink(wc_get_page_id('myaccount')), '/') . '/' . get_option('b2bking_offers_endpoint_setting', 'offers'));
    } else {
      $offerslink = $symbol = '';
    }

    $pageslug = '';
    if (isset($_GET['page'])) {
      $pageslug = sanitize_text_field($_GET['page']);
    } else if (isset($_GET['post_type'])) {
      $pageslug = sanitize_text_field($_GET['post_type']);
    } else if (isset($_GET['post'])) {
      $pageslug = sanitize_text_field($_GET['post']);
    }

    $translation_array = array(
      'admin_url' => get_admin_url(),
      'plugin_url' => plugins_url('', dirname(__FILE__)),
      'reject_redirection_url' => apply_filters('b2bking_reject_user_redirection_url', get_admin_url() . '/users.php'),
      'pageslug' => $pageslug,
      'security' => wp_create_nonce('b2bking_security_nonce'),
      'currency_symbol' => $symbol,
      'modified_currency_symbol' => apply_filters('b2bking_modified_offers_currency_symbol', 'SAR'),
      'are_you_sure_approve' => esc_html__('Are you sure you want to approve this user?', 'b2bking'),
      'are_you_sure_reject' => esc_html__('Are you sure you want to REJECT and DELETE this user? This is irreversible.', 'b2bking'),
      'are_you_sure_set_users' => esc_html__('Are you sure you want to move ALL users to this group?', 'b2bking'),
      'are_you_sure_deactivate' => esc_html__('Are you sure you want to DEACTIVATE this user? The user will no longer be approved and they will be unable to login.', 'b2bking'),
      'are_you_sure_set_categories' => esc_html__('Are you sure you want to set ALL categories / products?', 'b2bking'),
      'are_you_sure_set_subaccounts' => esc_html__('Are you sure you want to set these users as subaccounts of the parent?', 'b2bking'),
      'are_you_sure_set_subaccounts_regular' => esc_html__('Are you sure you want to set these users as regular accounts and no longer subaccounts?', 'b2bking'),
      'are_you_sure_update_user' => esc_html__('Are you sure you want to update this user\'s data?', 'b2bking'),
      'user_has_been_updated' => esc_html__('User data has been updated', 'b2bking'),
      'user_has_been_updated_vat_failed' => esc_html__('VAT VIES validation failed. Please check the VAT number you entered, or disable VIES validation in B2BKing > Registration Fields > VAT. Other fields have been successfully updated.', 'b2bking'),
      'categories_have_been_set' => esc_html__('All categories / products have been set successfully.', 'b2bking'),
      'subaccounts_have_been_set' => esc_html__('All subaccounts have been set', 'b2bking'),
      'feedback_sent' => esc_html__('Thank you. The feedback was sent successfully.', 'b2bking'),
      'username_already_list' => esc_html__('Username already in the list!', 'b2bking'),
      'add_user' => esc_html__('Add user', 'b2bking'),
      'type_to_search_users' => esc_html__('Type to search users...', 'b2bking'),
      'cart_total_quantity' => esc_html__('Cart Total Quantity', 'b2bking'),
      'cart_total_value' => esc_html__('Cart Total Value', 'b2bking'),
      'category_product_quantity' => esc_html__('Category Product Quantity', 'b2bking'),
      'category_product_value' => esc_html__('Category Product Value', 'b2bking'),
      'product_quantity' => esc_html__('Product Quantity', 'b2bking'),
      'product_value' => esc_html__('Product Value', 'b2bking'),
      'greater' => esc_html__('greater (>)', 'b2bking'),
      'equal' => esc_html__('equal (=)', 'b2bking'),
      'smaller' => esc_html__('smaller (<)', 'b2bking'),
      'delete' => esc_html__('Delete', 'b2bking'),
      'enter_quantity_value' => esc_html__('Enter the quantity/value', 'b2bking'),
      'add_condition' => esc_html__('Add Condition', 'b2bking'),
      'conditions_apply_cumulatively' => esc_html__('Conditions must apply cumulatively.', 'b2bking'),
      'conditions_multiselect' => esc_html__('Each category must meet all category conditions + cart total conditions. Each product must meet all product conditions + cart total conditions.', 'b2bking'),
      'purchase_lists_language_option' => get_option('b2bking_purchase_lists_language_setting', 'english'),
      'replace_product_selector' => intval(get_option('b2bking_replace_product_selector_setting', 1)),
      'replace_user_selector' => intval(1),
      'b2bking_customers_panel_ajax_setting' => intval(get_option('b2bking_customers_panel_ajax_setting', 0)),
      'b2bking_plugin_status_setting' => get_option('b2bking_plugin_status_setting', 'b2b'),
      'min_quantity_text' => esc_html__('Min. Quantity', 'b2bking'),
      'final_price_text' => apply_filters('b2bking_final_price_text', esc_html__('Final Price', 'b2bking')),
      'label_text' => esc_html__('Label', 'b2bking'),
      'text_text' => esc_html__('Text', 'b2bking'),
      'datatables_folder' => plugins_url('./includes/assets/lib/dataTables/i18n/', __FILE__),
      'print' => esc_html__('Print', 'b2bking'),
      'edit_columns' => esc_html__('Edit Columns', 'b2bking'),
      'group_rules_link' => admin_url('edit.php?post_type=b2bking_grule'),
      'dynamic_rules_link' => admin_url('edit.php?post_type=b2bking_rule'),
      'conversations_link' => admin_url('edit.php?post_type=b2bking_conversation'),
      'offers_link' => admin_url('edit.php?post_type=b2bking_offer'),
      'roles_link' => admin_url('edit.php?post_type=b2bking_custom_role'),
      'fields_link' => admin_url('edit.php?post_type=b2bking_custom_field'),
      'b2bgroups_link' => admin_url('edit.php?post_type=b2bking_group'),
      'goback_text' => esc_html__('Go back', 'b2bking'),
      'new_offer_link' => admin_url('/post-new.php?post_type=b2bking_offer'),
      'group_rules_text' => esc_html__('Set up group rules (optional)', 'b2bking'),
      'quote_fields_link' => admin_url('/edit.php?post_type=b2bking_quote_field'),
      'view_quote_fields' => esc_html__('Go to Quote Fields', 'b2bking'),
      'offer_details' => esc_html__('Offer details', 'b2bking'),
      'offer_custom_text' => esc_html__('Additional info', 'b2bking'),
      'item_name' => esc_html__('Item', 'b2bking'),
      'item_quantity' => esc_html__('Quantity', 'b2bking'),
      'unit_price' => esc_html__('Unit price', 'b2bking'),
      'item_subtotal' => esc_html__('Subtotal', 'b2bking'),
      'offer_total' => esc_html__('Total', 'b2bking'),
      'offers_logo' => get_option('b2bking_offers_logo_setting', ''),
      'offers_images_setting' => get_option('b2bking_offers_product_image_setting', 0),
      'offers_endpoint_link' => $offerslink,
      'offer_go_to' => esc_html__('-> Go to Offers', 'b2bking'),
      'email_offer_confirm' => esc_html__('This offer will be emailed to ALL users that have visibility, including all selected groups. Make sure to save the offer first if you made changes to it! Are you sure you want to proceed?', 'b2bking'),
      'confirm_duplicate' => esc_html__('Do you want to duplicate this item?', 'b2bking'),
      'duplicated_finish' => esc_html__('The item has been duplicated.', 'b2bking'),
      'yes_confirm' => esc_html__('Yes, I confirm', 'b2bking'),
      'are_you_sure' => esc_html__('Are you sure?', 'b2bking'),
      'issue_occurred' => esc_html__('An issue occurred', 'b2bking'),
      'success' => esc_html__('Success', 'b2bking'),
      'cancel' => esc_html__('Cancel', 'b2bking'),
      'email_has_been_sent' => esc_html__('The offer has been emailed successfully.', 'b2bking'),
      'value_conditions_error' => esc_html__('Value conditions (Cart Total Value, Product Value, Category Value) are not compatible with the "Apply discount as sale price" option. Please remove value conditions, or turn off this option.', 'b2bking'),
      'download_go_to_file' => intval(apply_filters('b2bking_download_file_go_to', 0)),
      'adminurl' => admin_url(),
      'pdf_download_lang' => apply_filters('b2bking_pdf_downloads_language', 'english'),
      'pdf_download_font' => apply_filters('b2bking_pdf_downloads_font', 'standard'),
      'caches_have_cleared' => esc_html__('All caches have been cleared', 'b2bking'),
      'caches_are_clearing' => esc_html__('Caches are clearing...', 'b2bking'),
      'sending_request' => esc_html__('Processing activation request...', 'b2bking'),
      'loaderurl' => plugins_url('./includes/assets/images/loaderpagegold5.svg', __FILE__),
      'ajax_pages_load' => apply_filters('b2bking_ajax_pages_load', get_option('b2bking_ajax_pages_load', 'enabled')), // disable ajax backend page load via snippets
      'dashboardstyleurl' => plugins_url('./assets/dashboard/cssjs/dashboardstyle.min.css', __FILE__),
      'inlineeditpostjsurl' => admin_url('js/inline-edit-post.js'),
      'commonjsurl' => plugins_url('./assets/js/common.js', __FILE__),
      'groupspage' => admin_url('admin.php?page=b2bking_groups'),
      'saving' => esc_html__('Saving settings...', 'b2bking'),
      'settings_changed' => esc_html__('Settings have changed, you should save them!', 'b2bking'),
      'settings_saved' => esc_html__('Settings saved successfully', 'b2bking'),
      'users_have_been_moved' => esc_html__('All users have been moved to your chosen group', 'b2bking'),
      'whitelabelname' => strtolower(esc_html__('b2bking', 'b2bking')),
      'custom_content_center_1' => apply_filters('b2bking_custom_content_offer_pdf_center_1', ''),
      'custom_content_center_2' => apply_filters('b2bking_custom_content_offer_pdf_center_2', ''),
      'custom_content_left_1' => apply_filters('b2bking_custom_content_offer_pdf_left_1', ''),
      'custom_content_left_2' => apply_filters('b2bking_custom_content_offer_pdf_left_2', ''),
      'custom_content_after_logo_center_1' => apply_filters('b2bking_custom_content_after_logo_offer_pdf_center_1', ''),
      'custom_content_after_logo_center_2' => apply_filters('b2bking_custom_content_after_logo_offer_pdf_center_2', ''),
      'custom_content_after_logo_left_1' => apply_filters('b2bking_custom_content_after_logo_offer_pdf_left_1', ''),
      'custom_content_after_logo_left_2' => apply_filters('b2bking_custom_content_after_logo_offer_pdf_left_2', ''),
      'mention_offer_requester' => apply_filters('b2bking_mention_offer_requester', ''),
      'registration_form_shortcodes_text' => esc_html__('Registration Form Shortcodes', 'b2bking'),
      'bulkorder_form_shortcodes_text' => esc_html__('Get Order Form Shortcodes', 'b2bking'),
      'sort_order_help_tip' => esc_html__('Drag & drop fields to arrange them in the order you would like them displayed on the frontend.', 'b2bking'),
      'form_preview_help_tip' => esc_html__('This is a preview of what the form may look like on the frontend.', 'b2bking'),
      'click_to_copy' => esc_html__('Click to Copy', 'b2bking'),
      'quick_edit' => esc_html__('Quick Edit', 'b2bking'),
      'duplicate' => esc_html__('Duplicate', 'b2bking'),
      'save_edit' => esc_html__('Save Edit', 'b2bking'),
      'copied' => esc_html__('Copied!', 'b2bking'),
      'select_all' => esc_html__('Select All', 'b2bking'),
      'unselect' => esc_html__('Unselect', 'b2bking'),
      'enabled' => esc_html__('Enabled', 'b2bking'),
      'disabled' => esc_html__('Disabled', 'b2bking'),
      'select_export_format' => esc_html__('Choose export format', 'b2bking'),
      'report_downloaded' => esc_html__('Report downloaded', 'b2bking'),
      'please_select_an_option' => esc_html__('Please select an option!', 'b2bking'),
      'offerlogowidth' => apply_filters('b2bking_offer_logo_width', 150),
      'offerlogotopmargin' => apply_filters('b2bking_offer_logo_top_margin', 0),
      'icons_text_message' => esc_html__('To add icons to the text below, you can add the shortcodes: [lock] , [login] , [wholesale] , [business] .', 'b2bking'),
      'offer_file_name' => apply_filters('b2bking_offer_file_name', 'offer'),
      'sure_deactivate_license' => esc_html__('This action will remove your license from this website.', 'b2bking'),
      'deactivating' => esc_html__('Deactivating...', 'b2bking'),
      'offers_disable_ajax' => intval(get_option('b2bking_offers_product_selector_setting', 0)),
      'search_for_products' => esc_html__('Search for products...', 'b2bking'),
      'switch_to_text' => esc_html__('Switch between product search and text', 'b2bking'),
      'switch_to_search' => esc_html__('Switch to search selector', 'b2bking'),


    );

    // generate HTML for toolbar
    ob_start();
    $active_number = get_option('b2bking_posts_per_page_backend_setting', 20);
    ?>
    <div class="b2bking_post_toolbar">
      <div class="b2bking_toolbar_selected_count b2bking_toolbar_selected_inactive">
        <span class="b2bking_toolbar_selected_count_number">3</span>
        <span class="b2bking_toolbar_selected_count_text"><?php esc_html_e('selected', 'b2bking'); ?></span>
      </div>
      <div id="b2bking_toolbar_settings_tab" class="b2bking_toolbar_settings_tab_inactive">
        <ul class="b2bking_toolbar_settings_list">
          <li><span class="b2bking_show_per_page"><?php esc_html_e('SHOW', 'b2bking'); ?></span></li>
          <li class="b2bking_show_per_page_number <?php if ($active_number == 20) {
            echo 'b2bking_active_page_number';
          } ?>">20</li>
          <li class="b2bking_show_per_page_number <?php if ($active_number == 50) {
            echo 'b2bking_active_page_number';
          } ?>">50</li>
          <li class="b2bking_show_per_page_number <?php if ($active_number == 100) {
            echo 'b2bking_active_page_number';
          } ?>">100</li>
        </ul>
      </div>
      <div class="b2bking_toolbar_select b2bking_select">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 20 20">
          <path fill="#A0A0A0"
            d="M17.08 4.69a1.875 1.875 0 0 1 1.253 1.768v8.334a3.542 3.542 0 0 1-3.541 3.541H6.458A1.875 1.875 0 0 1 4.69 17.08l1.748.003h8.355a2.291 2.291 0 0 0 2.291-2.291V6.458l-.003-.042V4.689Zm-2.708-3.023a1.875 1.875 0 0 1 1.875 1.875v10.83a1.875 1.875 0 0 1-1.875 1.875H3.542a1.875 1.875 0 0 1-1.875-1.874V3.542a1.875 1.875 0 0 1 1.875-1.875h10.83Zm-3.147 4.558-3.242 3.24-.816-1.09a.625.625 0 1 0-1 .75l1.25 1.667a.626.626 0 0 0 .941.066l3.75-3.75a.625.625 0 0 0-.883-.883Z" />
        </svg>
        <span class="b2bking_toolbar_select_text"><?php esc_html_e('Select All', 'b2bking'); ?></span>
      </div>
      <div class="b2bking_toolbar_enable_disable b2bking_toolbar_enable b2bking_toolbar_inactive">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
          <path fill="#A0A0A0" d="M17 7H7a5 5 0 1 0 0 10h10a5 5 0 1 0 0-10Zm0 8a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z" />
        </svg>
        <span class="b2bking_toolbar_select_text"><?php esc_html_e('Enable', 'b2bking'); ?></span>
      </div>
      <div class="b2bking_toolbar_enable_disable b2bking_toolbar_disable b2bking_toolbar_inactive">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
          <path fill="#A0A0A0"
            d="M17 6H7c-3.31 0-6 2.69-6 6s2.69 6 6 6h10c3.31 0 6-2.69 6-6s-2.69-6-6-6Zm0 10H7c-2.21 0-4-1.79-4-4s1.79-4 4-4h10c2.21 0 4 1.79 4 4s-1.79 4-4 4ZM7 9c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3Z" />
        </svg>
        <span class="b2bking_toolbar_select_text"><?php esc_html_e('Disable', 'b2bking'); ?></span>
      </div>
      <div id="b2bking_toolbar_settings">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 20 20"
          style="pointer-events: none">
          <g clip-path="url(#a)">
            <path stroke="#AEAEAE" stroke-linecap="square" stroke-linejoin="round"
              d="m7.925.667-.114.582-.439 2.131A7.36 7.36 0 0 0 5.46 4.477L3.316 3.76l-.576-.177-.299.513L.965 6.564l-.298.516.437.383 1.659 1.41c-.06.37-.138.734-.138 1.122 0 .388.078.753.138 1.122l-1.659 1.41-.437.382.298.515 1.476 2.467.299.516.576-.18 2.144-.716c.575.45 1.21.829 1.912 1.097l.439 2.13.114.583h4.148l.116-.582.438-2.131a7.368 7.368 0 0 0 1.912-1.097l2.144.716.576.18.3-.515 1.474-2.468.3-.515-.438-.382-1.659-1.411c.061-.37.137-.733.137-1.123 0-.386-.076-.752-.137-1.121l1.659-1.41.438-.383-.3-.515-1.474-2.467-.3-.514-.576.178-2.144.716a7.361 7.361 0 0 0-1.912-1.097l-.438-2.13-.116-.583H7.925Z"
              clip-rule="evenodd" />
            <path stroke="#AEAEAE" stroke-linecap="square" stroke-linejoin="round"
              d="M12.667 9.993a2.667 2.667 0 1 1-5.334 0 2.667 2.667 0 0 1 5.334 0Z" clip-rule="evenodd" />
          </g>
          <defs>
            <clipPath id="a">
              <path fill="#fff" d="M0 0h20v20H0z" />
            </clipPath>
          </defs>
        </svg>
      </div>


    </div>
    <?php
    $toolbar = ob_get_clean();
    $translation_array['toolbarhtml'] = $toolbar;
    // end HTML for toolbar

    // generate HTML for searchbar
    ob_start();

    ?>
    <div class="b2bking_post_searchbar">
      <input type="text" class="b2bking_searchbar_input" placeholder="<?php esc_html_e('Search items...', 'b2bking'); ?>"
        value="<?php
        if (isset($_GET['s'])) {
          echo esc_html($_GET['s']);
        }

        ?>">
      <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" fill="none" viewBox="0 0 19 19">
        <path fill="#A4A4A4"
          d="m17.219 16.38-4.484-4.485a6.542 6.542 0 1 0-.84.84l4.484 4.484.84-.84ZM2.375 7.718a5.344 5.344 0 1 1 10.688 0 5.344 5.344 0 0 1-10.688 0Z" />
      </svg>
    </div>
    <?php
    if (isset($_GET['s'])) {
      if (!empty($_GET['s'])) {
        // show clear button
        ?>
        <div class="b2bking_post_searchbar_clear">
          <span class="b2bking_post_searchbar_clear_text"><?php esc_html_e('Clear', 'b2bking'); ?></span>
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 20 20">
            <path fill="#919191"
              d="M10 1.667A8.326 8.326 0 0 1 18.333 10 8.326 8.326 0 0 1 10 18.333 8.326 8.326 0 0 1 1.667 10 8.326 8.326 0 0 1 10 1.667Zm2.992 4.166L10 8.825 7.008 5.833 5.833 7.008 8.825 10l-2.992 2.992 1.175 1.175L10 11.175l2.992 2.992 1.175-1.175L11.175 10l2.992-2.992-1.175-1.175Z" />
          </svg>
        </div>
        <?php
      }
    }

    $searchbar = ob_get_clean();
    $translation_array['searchbarhtml'] = $searchbar;

    $registration_roles = get_posts([
      'post_type' => 'b2bking_custom_role',
      'post_status' => 'publish',
      'numberposts' => -1,
    ]);

    $shtml =
      '<span class="b2bking_rform_rfor_title">Registration for:</span>
			<ul class="b2bking_registration_shortcodes_html_content">
			<li>
				<div class="b2bking_rform_left">' . esc_html__('All Groups', 'b2bking') . '</div>
				<div class="b2bking_rform_middle"><input type="checkbox" class="b2bking_rinclude_login_form" id="b2bking_rinclude_login_form_all"><label for="b2bking_rinclude_login_form_all">' . esc_html__('Add Login Form', 'b2bking') . '</label></div>
				<div class="b2bking_rform_right"><input type="text" id="b2bking_rshortcode_form_all" class="b2bking_rshortcode_form" value="[b2bking_b2b_registration_only]"><span class="dashicons dashicons-clipboard b2bking_rshortcode_icon"></span></div>
				<input type="hidden" class="roleid" value="all">
			</li>';

    foreach ($registration_roles as $role) {

      $non_selectable = intval(get_post_meta($role->ID, 'b2bking_non_selectable', true));

      if ($non_selectable !== 1) {
        $shtml .= '<li>
						<div class="b2bking_rform_left">' . esc_html($role->post_title) . '</div>
						<div class="b2bking_rform_middle"><input type="checkbox" class="b2bking_rinclude_login_form" id="b2bking_rinclude_login_form_' . esc_attr($role->ID) . '"><label for="b2bking_rinclude_login_form_' . esc_attr($role->ID) . '">' . esc_html__('Add Login Form', 'b2bking') . '</label></div>
						<div class="b2bking_rform_right"><input type="text" id="b2bking_rshortcode_form_' . esc_attr($role->ID) . '" class="b2bking_rshortcode_form" value="[b2bking_b2b_registration_only registration_role_id=' . esc_attr($role->ID) . ']"><span class="dashicons dashicons-clipboard b2bking_rshortcode_icon"></div>
						<input type="hidden" class="roleid" value="' . esc_attr($role->ID) . '">
					</li>';
      }
    }

    if (get_option('woocommerce_enable_myaccount_registration') !== 'yes') {
      $shtml .= '<li class="b2bking_please_enable_message"><a target="_blank" href="' . admin_url('admin.php?page=wc-settings&tab=account') . '">' . esc_html__('For login + registration forms to work correctly, please enable the following setting: WooCommerce -> Settings -> Accounts -> Allow customers to create an account on the "My account" page.', 'b2bking') . '</a></li>';
    }

    $shtml .= '<button type="button" id="b2bking_copied_rform">-</button></ul>';
    $translation_array['registration_form_shortcodes_html'] = $shtml;
    // end HTML for shortcodes button

    // generate HTML for bulk order form shortcodes display
    $bhtml =
      '<ul class="b2bking_registration_shortcodes_html_content">
			<li>
				<div class="b2bking_rform_left">' . esc_html__('Cream', 'b2bking') . '</div>
				<div class="b2bking_rform_middle"><img src="' . plugins_url('./includes/assets/images/creammin.png', __FILE__) . '"></div>
				<div class="b2bking_rform_right"><input type="text" id="b2bking_rshortcode_form_all" class="b2bking_rshortcode_form" value="[b2bking_bulkorder theme=cream]"><span class="dashicons dashicons-clipboard b2bking_rshortcode_icon"></span></div>
				<input type="hidden" class="roleid" value="all">
			</li>
			<li>
				<div class="b2bking_rform_left">' . esc_html__('Indigo', 'b2bking') . '</div>
				<div class="b2bking_rform_middle"><img src="' . plugins_url('./includes/assets/images/indigomin.png', __FILE__) . '"></div>
				<div class="b2bking_rform_right"><input type="text" id="b2bking_rshortcode_form_all" class="b2bking_rshortcode_form" value="[b2bking_bulkorder theme=indigo]"><span class="dashicons dashicons-clipboard b2bking_rshortcode_icon"></span></div>
				<input type="hidden" class="roleid" value="all">
			</li>
			<li>
				<div class="b2bking_rform_left">' . esc_html__('Classic', 'b2bking') . '</div>
				<div class="b2bking_rform_middle"><img src="' . plugins_url('./includes/assets/images/classicmin.png', __FILE__) . '"></div>
				<div class="b2bking_rform_right"><input type="text" id="b2bking_rshortcode_form_all" class="b2bking_rshortcode_form" value="[b2bking_bulkorder theme=classic]"><span class="dashicons dashicons-clipboard b2bking_rshortcode_icon"></span></div>
				<input type="hidden" class="roleid" value="all">
			</li>
			<div class="b2bking_shortcode_info_link"><a target="_blank" href="https://woocommerce-b2b-plugin.com/docs/wholesale-bulk-order-form/#3-toc-title">Get more info and advanced shortcode usage</a></div>
			<button type="button" id="b2bking_copied_rform">-</button></ul>';
    $translation_array['bulkorder_form_shortcodes_html'] = $bhtml;
    // end HTML for bulk order form shrotcodes

    if (isset($_GET['post'])) {
      $translation_array['current_post_type'] = get_post_type(sanitize_text_field($_GET['post']));
    }
    if (isset($_GET['action'])) {
      $translation_array['current_action'] = sanitize_text_field($_GET['action']);
    }

    // Group Rules Pro strings
    $translation_array['grpro_enabled'] = esc_html__('enabled', 'b2bking');
    $translation_array['grpro_disabled'] = esc_html__('disabled', 'b2bking');
    $translation_array['grpro_rule_status_updated'] = esc_html__('Rule %s successfully!', 'b2bking');
    $translation_array['grpro_failed_to_update_rule_status'] = esc_html__('Failed to update rule status.', 'b2bking');
    $translation_array['grpro_error_updating_rule_status'] = esc_html__('An error occurred while updating the rule status.', 'b2bking');
    $translation_array['grpro_confirm_delete_rule'] = esc_html__('Are you sure you want to delete this rule? This action cannot be undone.', 'b2bking');
    $translation_array['grpro_rule_deleted_successfully'] = esc_html__('Rule deleted successfully!', 'b2bking');
    $translation_array['grpro_failed_to_delete_rule'] = esc_html__('Failed to delete rule.', 'b2bking');
    $translation_array['grpro_error_deleting_rule'] = esc_html__('An error occurred while deleting the rule.', 'b2bking');
    $translation_array['grpro_failed_to_load_rules'] = esc_html__('Failed to load rules.', 'b2bking');
    $translation_array['grpro_error_loading_rules'] = esc_html__('An error occurred while loading rules.', 'b2bking');
    $translation_array['grpro_loading_rules'] = esc_html__('Loading rules...', 'b2bking');
    $translation_array['grpro_rule_order_updated'] = esc_html__('Rule order updated!', 'b2bking');
    $translation_array['grpro_failed_to_update_rule_order'] = esc_html__('Failed to update rule order.', 'b2bking');
    $translation_array['grpro_error_updating_rule_order'] = esc_html__('An error occurred while updating rule order.', 'b2bking');
    $translation_array['grpro_active'] = esc_html__('Active', 'b2bking');
    $translation_array['grpro_inactive'] = esc_html__('Inactive', 'b2bking');
    $translation_array['grpro_auto_move_customers'] = esc_html__('Automatically move customers from %1$s to %2$s when condition is met', 'b2bking');
    $translation_array['grpro_condition'] = esc_html__('Condition', 'b2bking');
    $translation_array['grpro_groups'] = esc_html__('Groups', 'b2bking');
    $translation_array['grpro_threshold'] = esc_html__('Threshold', 'b2bking');
    $translation_array['grpro_edit'] = esc_html__('Edit', 'b2bking');
    $translation_array['grpro_edit_group_rule'] = esc_html__('Edit Group Rule', 'b2bking');
    $translation_array['grpro_update_rule'] = esc_html__('Update Rule', 'b2bking');
    $translation_array['grpro_delete'] = esc_html__('Delete', 'b2bking');
    $translation_array['grpro_from'] = esc_html__('From', 'b2bking');
    $translation_array['grpro_to'] = esc_html__('To', 'b2bking');
    $translation_array['grpro_when'] = esc_html__('When', 'b2bking');
    $translation_array['grpro_rules_selected'] = esc_html__('%d rule(s) selected', 'b2bking');
    $translation_array['grpro_select_all'] = esc_html__('Select All', 'b2bking');
    $translation_array['grpro_deselect_all'] = esc_html__('Deselect All', 'b2bking');
    $translation_array['grpro_settings_saved_successfully'] = esc_html__('Settings saved successfully', 'b2bking');
    $translation_array['grpro_failed_to_save_settings'] = esc_html__('Failed to save settings', 'b2bking');
    $translation_array['grpro_error_saving_settings'] = esc_html__('An error occurred while saving settings', 'b2bking');
    $translation_array['grpro_confirm_delete_rules'] = esc_html__('Are you sure you want to delete %d rule(s)? This action cannot be undone.', 'b2bking');
    $translation_array['grpro_deleting_rules'] = esc_html__('Deleting %d rule(s)...', 'b2bking');
    $translation_array['grpro_rules_deleted_successfully'] = esc_html__('Successfully deleted %d rule(s)!', 'b2bking');
    $translation_array['grpro_failed_to_delete_rules'] = esc_html__('Failed to delete rules.', 'b2bking');

    // Group Rule Pro Editor strings
    $translation_array['grpro_confirm_cancel'] = esc_html__('Are you sure you want to cancel? Any unsaved changes will be lost.', 'b2bking');
    $translation_array['grpro_affected_customers_preview'] = esc_html__('This would show affected customers based on current rule conditions.', 'b2bking');
    $translation_array['grpro_total_amount_spent'] = esc_html__('total amount spent', 'b2bking');
    $translation_array['grpro_amount_spent_rolling'] = esc_html__('amount spent in last %d days', 'b2bking');
    $translation_array['grpro_amount_spent_yearly'] = esc_html__('amount spent in previous year', 'b2bking');
    $translation_array['grpro_amount_spent_quarterly'] = esc_html__('amount spent in previous quarter', 'b2bking');
    $translation_array['grpro_amount_spent_monthly'] = esc_html__('amount spent in previous month', 'b2bking');
    $translation_array['grpro_amount_spent_current_year'] = esc_html__('amount spent this year', 'b2bking');
    $translation_array['grpro_amount_spent_current_quarter'] = esc_html__('amount spent this quarter', 'b2bking');
    $translation_array['grpro_amount_spent_current_month'] = esc_html__('amount spent this month', 'b2bking');
    $translation_array['grpro_total_number_orders'] = esc_html__('total number of orders', 'b2bking');
    $translation_array['grpro_number_orders_rolling'] = esc_html__('number of orders in last %d days', 'b2bking');
    $translation_array['grpro_number_orders_yearly'] = esc_html__('number of orders in previous year', 'b2bking');
    $translation_array['grpro_number_orders_quarterly'] = esc_html__('number of orders in previous quarter', 'b2bking');
    $translation_array['grpro_number_orders_monthly'] = esc_html__('number of orders in previous month', 'b2bking');
    $translation_array['grpro_number_orders_current_year'] = esc_html__('number of orders this year', 'b2bking');
    $translation_array['grpro_number_orders_current_quarter'] = esc_html__('number of orders this quarter', 'b2bking');
    $translation_array['grpro_number_orders_current_month'] = esc_html__('number of orders this month', 'b2bking');
    $translation_array['grpro_days_since_first_order'] = esc_html__('days since first order', 'b2bking');
    $translation_array['grpro_days_since_last_order'] = esc_html__('days since last order', 'b2bking');
    $translation_array['grpro_greater_than'] = esc_html__('greater than', 'b2bking');
    $translation_array['grpro_greater_than_or_equal'] = esc_html__('greater than or equal to', 'b2bking');
    $translation_array['grpro_less_than'] = esc_html__('less than', 'b2bking');
    $translation_array['grpro_less_than_or_equal'] = esc_html__('less than or equal to', 'b2bking');
    $translation_array['grpro_between'] = esc_html__('between', 'b2bking');
    $translation_array['grpro_and'] = esc_html__('and', 'b2bking');
    $translation_array['grpro_selected_group'] = esc_html__('Selected Group', 'b2bking');
    $translation_array['grpro_all_groups'] = esc_html__('all groups', 'b2bking');
    $translation_array['grpro_rule_preview_template'] = esc_html__('When customers in %1$s have %2$s %3$s → move to %4$s', 'b2bking');
    $translation_array['grpro_configure_rule_preview'] = esc_html__('Configure the rule conditions to see a preview...', 'b2bking');
    $translation_array['grpro_move_to'] = esc_html__('Move to %s', 'b2bking');
    $translation_array['grpro_is'] = esc_html__('is', 'b2bking');
    $translation_array['grpro_when'] = esc_html__('When', 'b2bking');
    $translation_array['grpro_move_to_arrow'] = esc_html__('→ move to', 'b2bking');
    $translation_array['grpro_updating'] = esc_html__('Updating...', 'b2bking');
    $translation_array['grpro_creating'] = esc_html__('Creating...', 'b2bking');
    $translation_array['grpro_rule_saved_successfully'] = esc_html__('Rule saved successfully!', 'b2bking');
    $translation_array['grpro_error_saving_rule'] = esc_html__('An error occurred while saving the rule.', 'b2bking');
    $translation_array['grpro_error_saving_rule_retry'] = esc_html__('An error occurred while saving the rule. Please try again.', 'b2bking');
    $translation_array['grpro_rule_name_required'] = esc_html__('Rule name is required', 'b2bking');
    $translation_array['grpro_condition_type_required'] = esc_html__('Condition type is required', 'b2bking');
    $translation_array['grpro_operator_required'] = esc_html__('Operator is required', 'b2bking');
    $translation_array['grpro_between_values_required'] = esc_html__('Both minimum and maximum values are required for "between" operator', 'b2bking');
    $translation_array['grpro_threshold_value_required'] = esc_html__('Threshold value is required', 'b2bking');
    $translation_array['grpro_rolling_period_required'] = esc_html__('Rolling period is required for rolling conditions', 'b2bking');
    $translation_array['grpro_target_group_required'] = esc_html__('Target group is required', 'b2bking');
    $translation_array['grpro_source_groups_required'] = esc_html__('Source groups selection is required', 'b2bking');
    $translation_array['grpro_fix_errors'] = esc_html__('Please fix the following errors:', 'b2bking');

    // Dynamic Rules Pro strings
    $translation_array['drpro_cart_total_all_products'] = esc_html__('Cart total / All products', 'b2bking');
    $translation_array['drpro_specific_items'] = esc_html__('specific items', 'b2bking');
    $translation_array['drpro_all_products_except'] = esc_html__('all products except', 'b2bking');
    $translation_array['drpro_all_products_except_specific'] = esc_html__('all products except specific items', 'b2bking');
    $translation_array['drpro_all_products_except_specific_dots'] = esc_html__('All products except specific items...', 'b2bking');
    $translation_array['drpro_item'] = esc_html__('Item', 'b2bking');
    $translation_array['drpro_all_logged_in_users'] = esc_html__('All logged-in users', 'b2bking');
    $translation_array['drpro_b2b_customers_logged_in'] = esc_html__('B2B customers (logged-in)', 'b2bking');
    $translation_array['drpro_b2c_customers_logged_in'] = esc_html__('B2C customers (logged-in)', 'b2bking');
    $translation_array['drpro_guest_visitors'] = esc_html__('Guest visitors (not logged-in)', 'b2bking');
    $translation_array['drpro_multiple_audiences'] = esc_html__('multiple audiences', 'b2bking');
    $translation_array['drpro_audience'] = esc_html__('Audience', 'b2bking');
    $translation_array['drpro_user'] = esc_html__('User', 'b2bking');
    $translation_array['drpro_specific_users'] = esc_html__('specific users', 'b2bking');
    $translation_array['drpro_selected_b2b_group'] = esc_html__('Selected B2B Group', 'b2bking');
    $translation_array['drpro_configure_rule_preview'] = esc_html__('Configure your dynamic rule to see preview', 'b2bking');
    $translation_array['drpro_edit_dynamic_rule'] = esc_html__('Edit Dynamic Rule', 'b2bking');
    $translation_array['drpro_update_rule'] = esc_html__('Update Rule', 'b2bking');
    $translation_array['drpro_updating'] = esc_html__('Updating...', 'b2bking');
    $translation_array['drpro_rule_saved_successfully'] = esc_html__('Rule saved successfully', 'b2bking');
    $translation_array['drpro_select_countries'] = esc_html__('Select countries...', 'b2bking');
    $translation_array['drpro_select_user_types_groups'] = esc_html__('Select user types and groups...', 'b2bking');
    $translation_array['drpro_enter_item_name'] = esc_html__('Enter your item name here...', 'b2bking');
    $translation_array['drpro_search'] = esc_html__('Search...', 'b2bking');
    $translation_array['drpro_switch_to_brands'] = esc_html__('Switch to Brands? All existing dynamic rules will be converted from Tags to Brands. This change affects your entire workspace.', 'b2bking');
    $translation_array['drpro_switch_to_tags'] = esc_html__('Switch to Tags? All existing dynamic rules will be converted from Brands to Tags. This change affects your entire workspace.', 'b2bking');
    $translation_array['drpro_search_for_users'] = esc_html__('Search for users...', 'b2bking');
    $translation_array['drpro_show'] = esc_html__('Show', 'b2bking');
    $translation_array['drpro_hide'] = esc_html__('Hide', 'b2bking');
    $translation_array['drpro_remove'] = esc_html__('Remove', 'b2bking');
    $translation_array['drpro_remove_tier'] = esc_html__('Remove tier', 'b2bking');
    $translation_array['drpro_remove_row'] = esc_html__('Remove row', 'b2bking');
    $translation_array['drpro_min_quantity_text'] = esc_html__('Min. Quantity', 'b2bking');
    $translation_array['drpro_final_price_text'] = esc_html__('Final Price', 'b2bking');
    $translation_array['drpro_label_text'] = esc_html__('Label', 'b2bking');
    $translation_array['drpro_text_text'] = esc_html__('Text', 'b2bking');
    $translation_array['drpro_applies_to'] = esc_html__('Applies To', 'b2bking');
    $translation_array['drpro_users'] = esc_html__('Users', 'b2bking');
    $translation_array['drpro_value'] = esc_html__('Value', 'b2bking');
    $translation_array['drpro_rules_selected'] = esc_html__('rules selected', 'b2bking');
    $translation_array['drpro_select_all'] = esc_html__('Select All', 'b2bking');
    $translation_array['drpro_enable_selected'] = esc_html__('Enable Selected', 'b2bking');
    $translation_array['drpro_disable_selected'] = esc_html__('Disable Selected', 'b2bking');
    $translation_array['drpro_delete'] = esc_html__('Delete', 'b2bking');
    $translation_array['drpro_select_rule_type'] = esc_html__('— Select Rule Type —', 'b2bking');
    $translation_array['drpro_required'] = esc_html__('Required', 'b2bking');
    $translation_array['drpro_set_currency'] = esc_html__('Set Currency', 'b2bking');
    $translation_array['drpro_to'] = esc_html__('to', 'b2bking');
    $translation_array['drpro_of'] = esc_html__('of', 'b2bking');
    $translation_array['drpro_for'] = esc_html__('for', 'b2bking');
    $translation_array['drpro_on'] = esc_html__('on', 'b2bking');
    $translation_array['drpro_on_all_products_except'] = esc_html__('on all products except', 'b2bking');
    $translation_array['drpro_free_shipping_description'] = esc_html__('For free shipping dynamic rules to work, a free shipping method must be configured in WooCommerce settings for the customer\'s zone. <a href="https://woocommerce-b2b-plugin.com/docs/how-to-set-up-free-shipping-for-bulk-orders-dynamic-rules/#1-toc-title" target="_blank">Learn more</a>.', 'b2bking');
    $translation_array['drpro_payment_method_restriction_description'] = esc_html__('Restrict a payment method from being used when selected items are in the cart.', 'b2bking');
    $translation_array['drpro_shipping_method_restriction_description'] = esc_html__('Restrict a shipping method from being used when selected items are in the cart.', 'b2bking');

    // Early Access strings
    $translation_array['ea_network_error'] = esc_html__('Network error. Please try again.', 'b2bking');
    $translation_array['ea_error_occurred'] = esc_html__('An error occurred', 'b2bking');
    $translation_array['ea_feature_not_found'] = esc_html__('Feature not found', 'b2bking');
    $translation_array['ea_category_ui'] = esc_html__('UI/UX', 'b2bking');
    $translation_array['ea_category_functionality'] = esc_html__('Functionality', 'b2bking');
    $translation_array['ea_category_integration'] = esc_html__('Integration', 'b2bking');
    $translation_array['ea_category_performance'] = esc_html__('Performance', 'b2bking');
    $translation_array['ea_categories_label'] = esc_html__('Categories:', 'b2bking');
    $translation_array['ea_notes_label'] = esc_html__('Notes:', 'b2bking');

    return $translation_array;
  }

  public function get_dashboard_data(): array
  {

    $data = self::b2bking_get_dashboard_data();

    // Send data to JS
    return array(
      'days_sales_b2b' => apply_filters('b2bking_dashboard_days_sales_b2b', $data['days_sales_array']),
      'days_sales_b2c' => apply_filters('b2bking_dashboard_days_sales_b2c', $data['days_sales_b2c_array']),
      'hours_sales_b2b' => array_values($data['hours_sales_array']),
      'hours_sales_b2c' => array_values($data['hours_sales_b2c_array']),
      'b2bking_demo' => apply_filters('b2bking_is_dashboard_demo', 0),
      'currency_symbol' => get_woocommerce_currency_symbol(),
    );


  }

  public static function b2bking_get_dashboard_data(): array
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

  private static function convert_to_base_currency(
    object $order,
    float $value
  ): float {
    if (defined('WOOCS_VERSION')) {
      global $WOOCS;
      $order_currency = $order->get_currency();
      if ($order_currency && $order_currency != $WOOCS->default_currency) {
        $currencies = $WOOCS->get_currencies();
        if (
          isset($currencies[$order_currency]['rate'])
          && floatval($currencies[$order_currency]['rate']) > 0
        ) {
          $value = floatval($value) / floatval($currencies[$order_currency]['rate']);
        }
      }
    }
    return $value;
  }

  public function base_scripts(): void
  {

    wp_enqueue_style(
      'b2bking_admin_dashboard',
      plugins_url('./assets/dashboard/cssjs/dashboardstyle.min.css', __FILE__),
      $deps = array(),
      $ver = $this->version
    );

    wp_enqueue_script(
      'b2bking-popper',
      plugins_url('./assets/lib/popper/popper.min.js', __FILE__)
    );

    wp_enqueue_script(
      'b2bking-tippy',
      plugins_url('./assets/lib/popper/tippy.min.js', __FILE__)
    );

    wp_enqueue_script(
      'b2bking-sweetalert2',
      plugins_url('./assets/lib/sweetalert/sweetalert2.all.min.js', __FILE__),
      $deps = array(),
      $ver = $this->version
    );

    wp_enqueue_script(
      'b2bking_global_admin_notice_script',
      plugins_url('./assets/js/adminnotice.js', __FILE__),
      $deps = array(),
      $ver = $this->version,
      $in_footer = true
    );

    wp_enqueue_script(
      'dataTables',
      plugins_url('./assets/lib/dataTables/jquery.dataTables.min.js', __FILE__),
      $deps = array(),
      $ver = false,
      $in_footer = true
    );

    wp_enqueue_style(
      'dataTables',
      plugins_url('./assets/lib/dataTables/jquery.dataTables.min.css', __FILE__)
    );

    wp_enqueue_script(
      'dataTablesButtons',
      plugins_url('./assets/lib/dataTables/dataTables.buttons.min.js', __FILE__),
      $deps = array(),
      $ver = false,
      $in_footer = true
    );

    wp_enqueue_script(
      'dataTablesButtonsHTML',
      plugins_url('./assets/lib/dataTables/buttons.html5.min.js', __FILE__),
      $deps = array(),
      $ver = false,
      $in_footer = true
    );

    wp_enqueue_script(
      'dataTablesButtonsPrint',
      plugins_url('./assets/lib/dataTables/buttons.print.min.js', __FILE__),
      $deps = array(),
      $ver = false,
      $in_footer = true
    );

    wp_enqueue_script(
      'dataTablesButtonsColvis',
      plugins_url('./assets/lib/dataTables/buttons.colVis.min.js', __FILE__),
      $deps = array(),
      $ver = false,
      $in_footer = true
    );

    wp_enqueue_script(
      'jszip',
      plugins_url('./assets/lib/dataTables/jszip.min.js', __FILE__),
      $deps = array(),
      $ver = false,
      $in_footer = true
    );

    wp_enqueue_script(
      'pdfmake',
      plugins_url('./assets/lib/pdfmake/pdfmake.min.js', __FILE__),
      $deps = array(),
      $ver = false,
      $in_footer = true
    );

    wp_enqueue_script(
      'vfsfonts',
      plugins_url('./assets/lib/pdfmake/vfs_fonts.js', __FILE__),
      $deps = array(),
      $ver = false,
      $in_footer = true
    );

    wp_enqueue_style(
      'chartist',
      plugins_url('./assets/dashboard/chartist/chartist.min.css', __FILE__),
      $deps = array(),
      $ver = $this->version
    );

    wp_enqueue_script(
      'chartist',
      plugins_url('./assets/dashboard/chartist/chartist.min.js', __FILE__),
      $deps = array(),
      $ver = $this->version,
      $in_footer = true
    );

    wp_enqueue_script(
      'chartist-plugin-tooltip',
      plugins_url('./assets/dashboard/chartist/chartist-plugin-tooltip.min.js', __FILE__),
      $deps = array(),
      $ver = false,
      $in_footer = true
    );

    wp_enqueue_style(
      'select2',
      plugins_url('./assets/lib/select2/select2.min.css', __FILE__)
    );

    wp_enqueue_script(
      'select2',
      plugins_url('./assets/lib/select2/select2.min.js', __FILE__),
      array('jquery')
    );

  }
}
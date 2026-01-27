jQuery(document).ready(function ($) {

  if ($('.woocommerce-addresses').length) {
    initAddressManagement();
  }

  if ($('.woocommerce-shipping-addresses').length) {
    initCheckoutAddressSelector();
  }

  function initAddressManagement() {
    var addresses = wcMultipleAddresses.addresses || {};

    $('#add-new-address').on('click', function () {
      $('#address-form').show();
      $('#save-address-form')[0].reset();
      $('#address_id').val('');
    });

    $('#cancel-address').on('click', function () {
      $('#address-form').hide();
      $('#save-address-form')[0].reset();
    });

    $('.edit-address').on('click', function () {
      var addressId = $(this).data('id');
      var address = addresses[addressId];

      if (!address) return;

      $('#address_id').val(addressId);
      $('#first_name').val(address.first_name);
      $('#last_name').val(address.last_name);
      $('#company').val(address.company);
      $('#address_1').val(address.address_1);
      $('#address_2').val(address.address_2);
      $('#city').val(address.city);
      $('#state').val(address.state);
      $('#postcode').val(address.postcode);
      $('#country').val(address.country);
      $('#phone').val(address.phone);

      $('#address-form').show();
    });

    $('#save-address-form').on('submit', function (e) {
      e.preventDefault();

      $.ajax({
        url: wcMultipleAddresses.ajax_url,
        type: 'POST',
        data: {
          action: 'wc_save_address',
          nonce: wcMultipleAddresses.nonce,
          address_id: $('#address_id').val(),
          first_name: $('#first_name').val(),
          last_name: $('#last_name').val(),
          company: $('#company').val(),
          address_1: $('#address_1').val(),
          address_2: $('#address_2').val(),
          city: $('#city').val(),
          state: $('#state').val(),
          postcode: $('#postcode').val(),
          country: $('#country').val(),
          phone: $('#phone').val()
        },
        success: function (response) {
          if (response.success) {
            location.reload();
          } else {
            alert(response.data);
          }
        },
        error: function () {
          alert('An error occurred. Please try again.');
        }
      });
    });

    $('.delete-address').on('click', function () {
      if (!confirm(wcMultipleAddresses.delete_confirm || 'Are you sure you want to delete this address?')) {
        return;
      }

      var addressId = $(this).data('id');

      $.ajax({
        url: wcMultipleAddresses.ajax_url,
        type: 'POST',
        data: {
          action: 'wc_delete_address',
          nonce: wcMultipleAddresses.nonce,
          address_id: addressId
        },
        success: function (response) {
          if (response.success) {
            location.reload();
          }
        },
        error: function () {
          alert('An error occurred. Please try again.');
        }
      });
    });

    $('.set-default-address').on('click', function () {
      var addressId = $(this).data('id');

      $.ajax({
        url: wcMultipleAddresses.ajax_url,
        type: 'POST',
        data: {
          action: 'wc_set_default_address',
          nonce: wcMultipleAddresses.nonce,
          address_id: addressId
        },
        success: function (response) {
          if (response.success) {
            location.reload();
          }
        },
        error: function () {
          alert('An error occurred. Please try again.');
        }
      });
    });
  }

  function initCheckoutAddressSelector() {
    var addresses = wcMultipleAddresses.addresses || {};
    var defaultId = wcMultipleAddresses.default_address_id || '';

    $('#selected_address_id').on('change', function () {
      var addressId = $(this).val();
      var $shippingFields = $('.woocommerce-shipping-fields__field-wrapper');

      if (addressId && addresses[addressId]) {
        $shippingFields.hide();
        var address = addresses[addressId];
        $('#shipping_first_name').val(address.first_name);
        $('#shipping_last_name').val(address.last_name);
        $('#shipping_company').val(address.company);
        $('#shipping_address_1').val(address.address_1);
        $('#shipping_address_2').val(address.address_2);
        $('#shipping_city').val(address.city);
        $('#shipping_state').val(address.state);
        $('#shipping_postcode').val(address.postcode);
        $('#shipping_country').val(address.country).trigger('change');
      } else {
        $shippingFields.show();
        $('#shipping_first_name').val('');
        $('#shipping_last_name').val('');
        $('#shipping_company').val('');
        $('#shipping_address_1').val('');
        $('#shipping_address_2').val('');
        $('#shipping_city').val('');
        $('#shipping_state').val('');
        $('#shipping_postcode').val('');
        $('#shipping_country').val('US').trigger('change');
      }
    });

    // Trigger change on init if a value is selected (handled by defaultId logic below but also need to handle browser cached values)
    if ($('#selected_address_id').val()) {
      $('#selected_address_id').trigger('change');
    } else if (defaultId) {
      $('#selected_address_id').val(defaultId).trigger('change');
    }
  }

});
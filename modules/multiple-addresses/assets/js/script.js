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

      // Initialize autocomplete for new address form
      photonAddressAutocomplete('#address_1', function (data) {
        $('#address_1').val(data.line1);
        $('#city').val(data.city);
        $('#state').val(data.state);
        $('#shipping_state').val(data.state);
        $('#postcode').val(data.postcode);
        $('#country').val('US'); // Usually this is an input type text or select
      });
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

      // Initialize autocomplete for edit form too
      photonAddressAutocomplete('#address_1', function (data) {
        $('#address_1').val(data.line1);
        $('#city').val(data.city);
        $('#state').val(data.state);
        $('#postcode').val(data.postcode);
        $('#country').val('US');
      });
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

    // Initialize autocomplete for checkout
    photonAddressAutocomplete('#shipping_address_1', function (data) {
      $('#shipping_address_1').val(data.line1);
      $('#shipping_city').val(data.city);
      $('#shipping_state').val(data.state);
      $('#shipping_postcode').val(data.postcode);
      $('#shipping_country').val('US').trigger('change');
    });

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

    if ($('#selected_address_id').val()) {
      $('#selected_address_id').trigger('change');
    } else if (defaultId) {
      $('#selected_address_id').val(defaultId).trigger('change');
    }
  }

  function photonAddressAutocomplete(selector, onSelect) {
    if (!$(selector).length) return;

    $(selector).autocomplete({
      source: function (request, response) {
        $.ajax({
          url: "https://photon.komoot.io/api/",
          dataType: "json",
          data: {
            q: request.term,
            limit: 5,
            lang: 'en'
          },
          success: function (data) {
            var suggestions = [];
            if (data.features) {
              $.each(data.features, function (i, feature) {
                var props = feature.properties;

                if (props.countrycode === 'US' || props.country === 'United States') {
                  var label = props.name;
                  if (props.housenumber) label = props.housenumber + ' ' + props.street;
                  else if (props.street) label = props.street;

                  label += ', ' + props.city + ', ' + props.state + ' ' + props.postcode;

                  suggestions.push({
                    label: label,
                    value: props.housenumber ? props.housenumber + ' ' + props.street : props.street,
                    data: {
                      line1: props.housenumber ? props.housenumber + ' ' + props.street : props.street,
                      city: props.city,
                      state: props.state,
                      postcode: props.postcode,
                      country: 'US'
                    }
                  });
                }
              });
            }
            response(suggestions);
          }
        });
      },
      minLength: 3,
      select: function (event, ui) {
        if (onSelect) {
          onSelect(ui.item.data);
        }
      }
    });
  }

});
document.addEventListener('DOMContentLoaded', function () {

  const select = (selector) => document.querySelector(selector);
  const selectAll = (selector) => document.querySelectorAll(selector);
  const elemID = (element) => document.getElementById(element);

  if (select('.woocommerce-addresses')) {
    initAddressManagement();
  }

  if (select('.woocommerce-shipping-addresses')) {
    initCheckoutAddressSelector();
  }

  function initAddressManagement() {
    var addresses = wcMultipleAddresses.addresses || {};

    var addNewBtn = elemID('add-new-address');
    if (addNewBtn) {
      addNewBtn.addEventListener('click', function () {
        elemID('address-form').style.display = 'block';
        elemID('save-address-form').reset();
        elemID('address_id').value = '';

        photonAddressAutocomplete('#address_1', function (data) {
          setValueAndTrigger('#address_1', data.line1);
          setValueAndTrigger('#city', data.city);
          setValueAndTrigger('#state', data.state, true);
          setValueAndTrigger('#shipping_state', data.state, true);
          setValueAndTrigger('#postcode', data.postcode);
          setValueAndTrigger('#country', 'US', true);
        });
      });
    }

    var cancelBtn = elemID('cancel-address');
    if (cancelBtn) {
      cancelBtn.addEventListener('click', function () {
        elemID('address-form').style.display = 'none';
        elemID('save-address-form').reset();
      });
    }

    selectAll('.edit-address').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var addressId = this.dataset.id;
        var address = addresses[addressId];

        if (!address) return;

        elemID('address_id').value = addressId;
        elemID('first_name').value = address.first_name;
        elemID('last_name').value = address.last_name;
        elemID('company').value = address.company;
        elemID('address_1').value = address.address_1;
        elemID('address_2').value = address.address_2;
        elemID('city').value = address.city;
        elemID('state').value = address.state;
        elemID('postcode').value = address.postcode;
        elemID('country').value = address.country;
        elemID('phone').value = address.phone;

        elemID('address-form').style.display = 'block';

        photonAddressAutocomplete('#address_1', function (data) {
          setValueAndTrigger('#address_1', data.line1);
          setValueAndTrigger('#city', data.city);
          setValueAndTrigger('#state', data.state, true);
          setValueAndTrigger('#postcode', data.postcode);
          setValueAndTrigger('#country', 'US', true);
        });
      });
    });

    var saveForm = document.getElementById('save-address-form');
    if (saveForm) {
      saveForm.addEventListener('submit', function (e) {
        e.preventDefault();

        var formData = new URLSearchParams();
        formData.append('action', 'wc_save_address');
        formData.append('nonce', wcMultipleAddresses.nonce);
        formData.append('address_id', elemID('address_id').value);
        formData.append('first_name', elemID('first_name').value);
        formData.append('last_name', elemID('last_name').value);
        formData.append('company', elemID('company').value);
        formData.append('address_1', elemID('address_1').value);
        formData.append('address_2', elemID('address_2').value);
        formData.append('city', elemID('city').value);
        formData.append('state', elemID('state').value);
        formData.append('postcode', elemID('postcode').value);
        formData.append('country', elemID('country').value);
        formData.append('phone', elemID('phone').value);

        fetch(wcMultipleAddresses.ajax_url, {
          method: 'POST',
          body: formData,
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
          }
        })
          .then(function (response) {
            return response.json();
          })
          .then(function (response) {
            if (response.success) {
              location.reload();
            } else {
              alert(response.data);
            }
          })
          .catch(function () {
            alert('An error occurred. Please try again.');
          });
      });
    }

    selectAll('.delete-address').forEach(function (btn) {
      btn.addEventListener('click', function () {
        if (!confirm(wcMultipleAddresses.delete_confirm || 'Are you sure you want to delete this address?')) {
          return;
        }

        var addressId = this.dataset.id;
        var formData = new URLSearchParams();
        formData.append('action', 'wc_delete_address');
        formData.append('nonce', wcMultipleAddresses.nonce);
        formData.append('address_id', addressId);

        fetch(wcMultipleAddresses.ajax_url, {
          method: 'POST',
          body: formData,
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
          }
        })
          .then(function (response) {
            return response.json();
          })
          .then(function (response) {
            if (response.success) {
              location.reload();
            }
          })
          .catch(function () {
            alert('An error occurred. Please try again.');
          });
      });
    });

    selectAll('.set-default-address').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var addressId = this.dataset.id;
        var formData = new URLSearchParams();
        formData.append('action', 'wc_set_default_address');
        formData.append('nonce', wcMultipleAddresses.nonce);
        formData.append('address_id', addressId);

        fetch(wcMultipleAddresses.ajax_url, {
          method: 'POST',
          body: formData,
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
          }
        })
          .then(function (response) {
            return response.json();
          })
          .then(function (response) {
            if (response.success) {
              location.reload();
            }
          })
          .catch(function () {
            alert('An error occurred. Please try again.');
          });
      });
    });
  }

  function initCheckoutAddressSelector() {
    var addresses = wcMultipleAddresses.addresses || {};
    var defaultId = wcMultipleAddresses.default_address_id || '';

    photonAddressAutocomplete('#shipping_address_1', function (data) {
      setValueAndTrigger('#shipping_address_1', data.line1);
      setValueAndTrigger('#shipping_city', data.city);
      setValueAndTrigger('#shipping_state', data.state, true);
      setValueAndTrigger('#shipping_postcode', data.postcode);
      setValueAndTrigger('#shipping_country', 'US', true);
    });

    var selectedAddressId = elemID('selected_address_id');
    if (selectedAddressId) {
      selectedAddressId.addEventListener('change', function () {
        var addressId = this.value;
        var shippingFields = select('.woocommerce-shipping-fields__field-wrapper');

        if (addressId && addresses[addressId]) {
          if (shippingFields) shippingFields.style.display = 'none';
          var address = addresses[addressId];
          elemID('shipping_first_name').value = address.first_name;
          elemID('shipping_last_name').value = address.last_name;
          elemID('shipping_company').value = address.company;
          elemID('shipping_address_1').value = address.address_1;
          elemID('shipping_address_2').value = address.address_2;
          elemID('shipping_city').value = address.city;
          elemID('shipping_state').value = address.state;
          elemID('shipping_postcode').value = address.postcode;
          setValueAndTrigger('#shipping_country', address.country, true);
        } else {
          if (shippingFields) shippingFields.style.display = 'block';
          elemID('shipping_first_name').value = '';
          elemID('shipping_last_name').value = '';
          elemID('shipping_company').value = '';
          elemID('shipping_address_1').value = '';
          elemID('shipping_address_2').value = '';
          elemID('shipping_city').value = '';
          elemID('shipping_state').value = '';
          elemID('shipping_postcode').value = '';
          setValueAndTrigger('#shipping_country', 'US', true);
        }
      });

      if (selectedAddressId.value) {
        selectedAddressId.dispatchEvent(new Event('change'));
      } else if (defaultId) {
        selectedAddressId.value = defaultId;
        selectedAddressId.dispatchEvent(new Event('change'));
      }
    }
  }

  function setValueAndTrigger(selector, value, changeEvent) {
    var el = select(selector);
    if (el) {
      el.value = value;
      if (changeEvent) {
        el.dispatchEvent(new Event('change', { bubbles: true }));
      }
    }
  }

  function photonAddressAutocomplete(selector, onSelect) {
    if (typeof jQuery === 'undefined' || !jQuery(selector).length) return;

    var usStates = {
      "Alabama": "AL", "Alaska": "AK", "Arizona": "AZ", "Arkansas": "AR", "California": "CA",
      "Colorado": "CO", "Connecticut": "CT", "Delaware": "DE", "Florida": "FL", "Georgia": "GA",
      "Hawaii": "HI", "Idaho": "ID", "Illinois": "IL", "Indiana": "IN", "Iowa": "IA",
      "Kansas": "KS", "Kentucky": "KY", "Louisiana": "LA", "Maine": "ME", "Maryland": "MD",
      "Massachusetts": "MA", "Michigan": "MI", "Minnesota": "MN", "Mississippi": "MS", "Missouri": "MO",
      "Montana": "MT", "Nebraska": "NE", "Nevada": "NV", "New Hampshire": "NH", "New Jersey": "NJ",
      "New Mexico": "NM", "New York": "NY", "North Carolina": "NC", "North Dakota": "ND", "Ohio": "OH",
      "Oklahoma": "OK", "Oregon": "OR", "Pennsylvania": "PA", "Rhode Island": "RI", "South Carolina": "SC",
      "South Dakota": "SD", "Tennessee": "TN", "Texas": "TX", "Utah": "UT", "Vermont": "VT",
      "Virginia": "VA", "Washington": "WA", "West Virginia": "WV", "Wisconsin": "WI", "Wyoming": "WY",
      "District of Columbia": "DC"
    };

    jQuery(selector).autocomplete({
      source: function (request, response) {
        var url = new URL("https://photon.komoot.io/api/");
        url.searchParams.append('q', request.term);
        url.searchParams.append('limit', 20);
        url.searchParams.append('lang', 'en');

        fetch(url)
          .then(function (res) {
            return res.json();
          })
          .then(function (data) {
            var suggestions = [];
            if (data.features) {
              data.features.forEach(function (feature) {
                var props = feature.properties;

                if (props.countrycode === 'US' || props.country === 'United States') {
                  var label = props.name;
                  if (props.housenumber) label = props.housenumber + ' ' + props.street;
                  else if (props.street) label = props.street;

                  label += ', ' + props.city + ', ' + props.state + ' ' + props.postcode;

                  var stateCode = usStates[props.state] || props.state;

                  suggestions.push({
                    label: label,
                    value: props.housenumber ? props.housenumber + ' ' + props.street : props.street,
                    data: {
                      line1: props.housenumber ? props.housenumber + ' ' + props.street : props.street,
                      city: props.city,
                      state: stateCode,
                      postcode: props.postcode,
                      country: 'US'
                    }
                  });
                }
              });
            }
            response(suggestions);
          })
          .catch(function (error) {
            console.error('Error fetching address suggestions:', error);
            response([]);
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
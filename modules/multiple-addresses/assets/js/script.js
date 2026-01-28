document.addEventListener('DOMContentLoaded', () => {
  if (typeof jQuery === 'undefined') {
    console.error('[Multiple Addresses] jQuery is not defined');
    return;
  }
  if (typeof jQuery.ui === 'undefined') {
    console.error('[Multiple Addresses] jQuery UI is not defined');
    return;
  }

  const $ = jQuery;
  const select = (selector) => document.querySelector(selector);
  const selectAll = (selector) => document.querySelectorAll(selector);
  const elemID = (element) => document.getElementById(element);
  const headersTypeForm = {
    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
  };

  const initAddressManagement = () => {
    const addresses = wcMultipleAddresses.addresses || {};

    const addNewBtn = elemID('add-new-address');
    if (addNewBtn) {
      addNewBtn.addEventListener('click', () => {
        elemID('address-form').style.display = 'block';
        elemID('save-address-form').reset();
        elemID('address_id').value = '';

        photonAddressAutocomplete('#address_1', (data) => {
          const fields = [
            ['#address_1', data.line1],
            ['#city', data.city],
            ['#state', data.state, true],
            ['#shipping_state', data.state, true],
            ['#postcode', data.postcode],
            ['#country', 'US', true],
          ];

          fields.forEach(([selector, value, trigger]) => {
            setValueAndTrigger(selector, value, trigger);
          });
        });
      });
    }

    const cancelBtn = elemID('cancel-address');
    if (cancelBtn) {
      cancelBtn.addEventListener('click', () => {
        elemID('address-form').style.display = 'none';
        elemID('save-address-form').reset();
      });
    }

    selectAll('.edit-address').forEach((btn) => {
      btn.addEventListener('click', (e) => {
        const addressId = e.target.dataset.id;
        const address = addresses[addressId];

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

        photonAddressAutocomplete('#address_1', (data) => {
          const fields = [
            ['#address_1', data.line1],
            ['#city', data.city],
            ['#state', data.state, true],
            ['#postcode', data.postcode],
            ['#country', 'US', true]
          ];

          fields.forEach(([selector, value, trigger]) => {
            setValueAndTrigger(selector, value, trigger);
          });
        });
      });
    });

    const saveForm = document.getElementById('save-address-form');
    if (saveForm) {
      saveForm.addEventListener('submit', (e) => {
        e.preventDefault();

        const formData = new URLSearchParams();
        const values = [
          ['action', 'wc_save_address'],
          ['nonce', wcMultipleAddresses.nonce],
          ['address_id', elemID('address_id').value],
          ['first_name', elemID('first_name').value],
          ['last_name', elemID('last_name').value],
          ['company', elemID('company').value],
          ['address_1', elemID('address_1').value],
          ['address_2', elemID('address_2').value],
          ['city', elemID('city').value],
          ['state', elemID('state').value],
          ['postcode', elemID('postcode').value],
          ['country', elemID('country').value],
          ['phone', elemID('phone').value],
        ];

        values.forEach(([key, value]) => {
          formData.append(key, value);
        });

        fetch(wcMultipleAddresses.ajax_url, {
          method: 'POST',
          body: formData,
          headers: headersTypeForm
        })
          .then((response) => response.json())
          .then((response) => {
            if (response.success) {
              location.reload();
            } else {
              alert(response.data);
            }
          })
          .catch(() => {
            alert('An error occurred. Please try again.');
          });
      });
    }

    selectAll('.delete-address').forEach((btn) => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();

        if (
          !confirm(wcMultipleAddresses.delete_confirm
            || 'Are you sure you want to delete this address?')
        ) {
          return;
        }

        const addressId = btn.dataset.id;
        const formData = new URLSearchParams();
        const values = [
          ['action', 'wc_delete_address'],
          ['nonce', wcMultipleAddresses.nonce],
          ['address_id', addressId],
        ];

        values.forEach(([key, value]) => {
          formData.append(key, value);
        });

        fetch(wcMultipleAddresses.ajax_url, {
          method: 'POST',
          body: formData,
          headers: headersTypeForm
        })
          .then((response) => response.json())
          .then((response) => {
            if (response.success) {
              location.reload();
            }
          })
          .catch(() => {
            alert('An error occurred. Please try again.');
          });
      });
    });

    selectAll('.set-default-address').forEach((btn) => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();

        const addressId = btn.dataset.id;
        const formData = new URLSearchParams();
        formData.append('action', 'wc_set_default_address');
        formData.append('nonce', wcMultipleAddresses.nonce);
        formData.append('address_id', addressId);

        fetch(wcMultipleAddresses.ajax_url, {
          method: 'POST',
          body: formData,
          headers: headersTypeForm
        })
          .then((response) => response.json())
          .then((response) => response.success && location.reload())
          .catch(() => alert('An error occurred. Please try again.'));
      });
    });
  }

  const initCheckoutAddressSelector = () => {
    const addresses = wcMultipleAddresses.addresses || {};
    const defaultId = wcMultipleAddresses.default_address_id || '';

    photonAddressAutocomplete('#shipping_address_1', (data) => {
      const fields = [
        ['#shipping_address_1', data.line1],
        ['#shipping_city', data.city],
        ['#shipping_state', data.state, true],
        ['#shipping_postcode', data.postcode],
        ['#shipping_country', 'US', true]
      ];

      fields.forEach(([selector, value, trigger]) => {
        setValueAndTrigger(selector, value, trigger);
      });
    });

    const selectedAddressId = elemID('selected_address_id');
    if (selectedAddressId) {
      selectedAddressId.addEventListener('change', () => {
        const addressId = this.value;
        const shippingFields = select(
          '.woocommerce-shipping-fields__field-wrapper');

        if (addressId && addresses[addressId]) {
          if (shippingFields) shippingFields.style.display = 'none';
          const address = addresses[addressId];
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

  const setValueAndTrigger = (selector, value, changeEvent) => {
    const el = select(selector);
    if (el) {
      el.value = value;
      if (changeEvent) {
        el.dispatchEvent(new Event('change', { bubbles: true }));
      }
    }
  }

  const photonAddressAutocomplete = (selector, onSelect) => {
    if (!$(selector).length) return;

    const usStates = wcMultipleAddresses.us_states;

    $(selector).autocomplete({
      source: (request, response) => {
        const url = new URL(wcMultipleAddresses.photon_url);
        url.searchParams.append('q', request.term);
        url.searchParams.append('limit', 20);
        url.searchParams.append('lang', 'en');

        fetch(url)
          .then((res) => res.json())
          .then((data) => createSuggestions(data, usStates, response))
          .catch((error) => {
            console.error(
              '[Multiple Addresses] Error fetching address suggestions:',
              error
            );
            response([]);
          });
      },
      classes: {
        'ui-autocomplete': 'wc-multiple-addresses-autocomplete'
      },
      minLength: 3,
      select: (event, ui) => {
        if (onSelect) {
          onSelect(ui.item.data);
        }
      }
    });
  }

  const createSuggestions = (data, usStates, response) => {
    const suggestions = [];
    if (data.features) {
      data.features.forEach((feature) => {
        const props = feature.properties;

        if (props.countrycode === 'US' || props.country === 'United States') {
          const street = props.street;
          const city = props.city;
          const state = props.state;
          const postcode = props.postcode;
          const stateCode = usStates[state] || state;
          let housenumber = props.housenumber;
          let label = props.name;

          if (
            typeof city === 'undefined'
            || typeof state === 'undefined'
            || typeof postcode === 'undefined'
          ) {
            return;
          }

          if (housenumber) {
            housenumber = housenumber + ' ' + street;
            label = housenumber;
          } else if (street) {
            label = street;
            housenumber = street;
          } else {
            housenumber = label;
          }

          label = label + ', ' + city + ', ' + stateCode + ', ' + postcode;

          suggestions.push({
            label: label,
            value: housenumber,
            data: {
              line1: housenumber,
              city: city,
              state: stateCode,
              postcode: postcode,
              country: 'US'
            }
          });
        }
      });
    }
    response(suggestions);
  }

  if (select('.woocommerce-addresses')) {
    initAddressManagement();
  }

  if (select('.woocommerce-shipping-addresses')) {
    initCheckoutAddressSelector();
  }

});
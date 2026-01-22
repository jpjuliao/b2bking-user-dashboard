jQuery(document).ready(function ($) {
  $(document).on('click', '.upload-lab-report', function (e) {
    e.preventDefault();

    const button = $(this);
    const loop = button.data('loop');

    const mediaUploader = wp.media({
      title: 'Select Lab Report PDF',
      button: {
        text: 'Use this PDF'
      },
      library: {
        type: 'application/pdf'
      },
      multiple: false
    });

    mediaUploader.on('select', function () {
      const attachment = mediaUploader.state().get('selection').first().toJSON();
      $('#lab-report-id-' + loop).val(attachment.id).trigger('change');
      $('#lab-report-filename-' + loop).text(attachment.filename);
      button.text('Change PDF');
      button.siblings('.remove-lab-report').show();
      $('#variable_product_options').trigger('woocommerce_variations_input_changed');
    });

    mediaUploader.open();
  });

  $(document).on('click', '.remove-lab-report', function (e) {
    e.preventDefault();

    const button = $(this);
    const loop = button.data('loop');

    $('#lab-report-id-' + loop).val('').trigger('change');
    $('#lab-report-filename-' + loop).text('');
    button.siblings('.upload-lab-report').text('Upload PDF');
    button.hide();
    $('#variable_product_options').trigger('woocommerce_variations_input_changed');
  });
});
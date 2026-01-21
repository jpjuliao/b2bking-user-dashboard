jQuery(document).ready(function ($) {
  let mediaUploader;

  $(document).on('click', '.upload-lab-report', function (e) {
    e.preventDefault();

    const button = $(this);
    const loop = button.data('loop');
    const container = button.closest('.form-row');

    if (mediaUploader) {
      mediaUploader.open();
      return;
    }

    mediaUploader = wp.media({
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
      container.find('.lab-report-id').val(attachment.id);
      container.find('.lab-report-filename').text(attachment.filename);
      button.text('Change PDF');
      container.find('.remove-lab-report').show();
    });

    mediaUploader.open();
  });

  $(document).on('click', '.remove-lab-report', function (e) {
    e.preventDefault();

    const button = $(this);
    const container = button.closest('.form-row');

    container.find('.lab-report-id').val('');
    container.find('.lab-report-filename').text('');
    container.find('.upload-lab-report').text('Upload PDF');
    button.hide();
  });
});
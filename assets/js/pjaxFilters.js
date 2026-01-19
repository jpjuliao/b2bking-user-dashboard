(function () {

  document.addEventListener('change', function (e) {
    if (e.target.matches('.filter-checkboxes input[type="checkbox"]')) {
      shop_filters_pjax();
    }
  });

  document.addEventListener('click', function (e) {
    if (e.target.closest('.shop-filters-form .apply-filters-btn')) {
      e.preventDefault();
      shop_filters_pjax();
    }
  });

  function shop_filters_pjax() {
    var form = document.querySelector('.shop-filters-form');

    if (
      typeof jQuery === 'undefined' || typeof jQuery.fn.pjax === 'undefined'
    ) {
      console.warn('jQuery or PJAX not loaded.');
      return;
    }

    jQuery.pjax({
      container: '.wd-page-content',
      timeout: typeof woodmart_settings !== 'undefined' ? woodmart_settings.pjax_timeout : 10000,
      url: form.getAttribute('action'),
      data: jQuery(form).serialize(),
      scrollTo: false,
      renderCallback: function (context, html, afterRender) {
        if (typeof woodmartThemeModule !== 'undefined') {
          woodmartThemeModule.removeDuplicatedStylesFromHTML(
            html, function (html) {
              context.html(html);
              afterRender();
              woodmartThemeModule.$document.trigger('wdShopPageInit');
              woodmartThemeModule.$document.trigger('wood-images-loaded');
            });
        } else {
          context.html(html);
          afterRender();
        }
      }
    });
  }

})();

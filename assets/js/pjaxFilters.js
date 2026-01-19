(function ($) {

  woodmartThemeModule.$document.on(
    'click', '.shop-filters-form .apply-filters-btn', shop_filters_pjax);

  function shop_filters_pjax() {
    var form = $('.shop-filters-form');
    $.pjax({
      container: '.wd-page-content',
      timeout: woodmart_settings.pjax_timeout,
      url: form.attr('action'),
      data: form.serialize(),
      scrollTo: false,
      renderCallback: function (context, html, afterRender) {
        woodmartThemeModule.removeDuplicatedStylesFromHTML(
          html, function (html) {
            context.html(html);
            afterRender();
            woodmartThemeModule.$document.trigger('wdShopPageInit');
            woodmartThemeModule.$document.trigger('wood-images-loaded');
          });
      }
    });

    return false;
  }

})(jQuery);

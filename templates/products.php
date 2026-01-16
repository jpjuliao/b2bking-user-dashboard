<div class="b2bk_user_dashboard_popular_products">
  <h3><?php echo esc_html__('Popular Products', 'b2bking'); ?></h3>
  <div class="row">
    <?php foreach ($products as $product_id => $quantity): ?>
      <?php $link = get_the_permalink($product_id); ?>
      <div class="col-3">
        <div class="product-item">
          <a class="product-link" href="<?php echo esc_url($link); ?>">
            <?php echo get_the_post_thumbnail($product_id, 'thumbnail'); ?>
            <div class="product-title">
              <?php echo esc_html(get_the_title($product_id)); ?>
            </div>
          </a>
          <div class="product-add-to-cart">
            <?php echo do_shortcode(
              '[add_to_cart id="' . $product_id . '"]'
            ); ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
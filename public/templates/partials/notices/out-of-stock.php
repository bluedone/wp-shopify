<?php

/*

@description   Used for displaying "out of stock" and any related add to cart errors

@version       1.0.0
@since         1.0.49
@path          templates/partials/notices/out-of-stock.php

@docs          https://wpshop.io/docs/templates/partials/notices/out-of-stock

*/

if ( !defined('ABSPATH') ) {
	exit;
}

?>

<aside class="wps-notice-inline wps-product-notice wps-notice-inline-sm wps-is-visible wps-notice-warning <?php echo apply_filters('wps_out_of_stock_class', ''); ?>">
  <?php esc_html_e('Out of stock', 'wp-shopify'); ?>
</aside>

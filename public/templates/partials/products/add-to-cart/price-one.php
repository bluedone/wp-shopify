<?php

/*

@description   Single price component. Used when product has one price, e.g., Small: $1, Medium: $1, Large: $1.
							 Used on both product single and product listing pages.

@version       1.0.0
@since         1.0.49
@path          templates/partials/products/add-to-cart/price-one.php

@docs          https://wpshop.io/docs/templates/partials/products/add-to-cart/price-one

*/

if ( !defined('ABSPATH') ) {
	exit;
}

?>

<h3
  itemprop="offers"
  itemscope
  itemtype="https://schema.org/Offer"
  class="wps-products-price wps-products-price-one <?= apply_filters( 'wps_products_price_class', '' ); ?>">

	<!-- <span class="compare_at_price" style="text-decoration: line-through;">$25.00</span> -->

  <?= apply_filters('wps_products_price_one', $data->price, $data->product); ?>

</h3>

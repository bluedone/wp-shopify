<?php

/*

@description   Single product on single collection page

@version       1.0.4
@since         1.0.49
@path          templates/partials/collections/single/product.php

@docs          https://wpshop.io/docs/templates/collections/single/product

*/

if ( !defined('ABSPATH') ) {
	exit;
}

use WPS\DB\Images;
use WPS\Factories\Money_Factory;

$Money = Money_Factory::build();
$image = Images::get_image_details_from_product($data->product);

?>

<li
  itemscope
  itemtype="https://schema.org/Offer"
  class="wps-col wps-col-3 wps-collection-single-product">

  <a
    href="<?= apply_filters( 'wps_products_link', esc_url( home_url() . '/' . $data->settings->url_products . '/' . $data->product->handle ), $data->product ); ?>"
    class="wps-collections-product-link"
    title="<?php esc_attr_e($data->product->title . ' ' . $data->product->product_id . ' ' . $data->product->product_type, WPS_PLUGIN_TEXT_DOMAIN ); ?>">

    <img
      itemprop="image"
      src="<?= esc_url($image->src); ?>" alt="<?php esc_attr_e($image->alt, WPS_PLUGIN_TEXT_DOMAIN); ?>" class="wps-products-img" />

    <h2
      itemprop="name"
      class="wps-collections-product-title wps-products-title">
      <?php esc_html_e($data->product->title, WPS_PLUGIN_TEXT_DOMAIN); ?>
    </h2>

		<?php do_action('wps_product_single_header_price_before', $data->product); ?>

    <h3 class="wps-products-price">
      <?= $Money->format_money($data->product->variants[0]->price, $data->product->variants[0]); ?>
    </h3>

		<?php do_action('wps_product_single_header_price_after', $data->product); ?>

  </a>

</li>

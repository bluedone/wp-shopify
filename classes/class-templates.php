<?php

namespace WPS;

use WPS\Template_Loader;
use WPS\Utils;
use WPS\Config;
use WPS\DB\Settings_General;
use WPS\DB\Images;
use WPS\DB\Variants;
use WPS\DB\Products;


// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}


/*

Hooks Class

*/
if (!class_exists('Templates')) {

	class Templates {

		protected static $instantiated = null;
		public $template_loader = null;

    /*

    Initialize the class and set its properties.

    */
    public function __construct() {
			$this->template_loader = new Template_Loader;
    }


		/*

		Creates a new class if one hasn't already been created.
		Ensures only one instance is used.

		*/
		public static function instance() {

			if (is_null(self::$instantiated)) {
				self::$instantiated = new self();
			}

			return self::$instantiated;

		}


		/*

		Template: partials/products/loop/loop-start

		*/
		public function wps_products_loop_start($query) {

			$data = [
				'query' => $query
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/loop/start' );

		}


		/*

		Template: partials/products/loop/loop-end

		*/
		public function wps_products_loop_end($products) {

			$data = [
				'products' => $products
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/loop/end' );

		}


		/*

		Template: partials/products/loop/item-start

		*/
		public function wps_products_item_start($product, $args, $customArgs) {


			// Related products will always override
			if (isset($args->wps_related_products_items_per_row) && $args->wps_related_products_items_per_row !== false ) {
				$items_per_row = $args->wps_related_products_items_per_row;

			} else {
				$items_per_row = apply_filters('wps_products_items_per_row', 3);
			}


			// Shortcode will always override wps_related_products_items_per_row filter
			if (isset($customArgs['items-per-row']) && $customArgs['items-per-row'] !== false ) {
				$items_per_row = $customArgs['items-per-row'];

			} else {
				$items_per_row = apply_filters('wps_products_items_per_row', 3);
			}


			$data = [
				'product' 			=> $product,
				'args' 					=> $args,
				'custom_args' 	=> $customArgs,
				'items_per_row' => $items_per_row
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/loop/item', 'start' );

		}


		/*

		Template: partials/products/loop/item-end

		*/
		public function wps_products_item_end($product) {

			$data = [
				'product' => $product
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/loop/item', 'end' );

		}


		/*

		Template: partials/products/loop/item

		*/
		public function wps_products_item($product, $args, $settings) {

			$Products = new Products();

			$data = [
				'product' 					=> 	$product,
				'product_details'		=>	$Products->get_data($product->post_id),
				'args'							=>	$args,
				'settings'					=>	$settings
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/loop/item' );

		}


		/*

		Template: partials/products/loop/item-link-start

		*/
		public function wps_products_item_link_start($product, $settings) {

			$data = [
				'product' 		=> 	$product,
				'settings'		=>	$settings
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/loop/item-link', 'start' );

		}


		/*

		Template: partials/products/loop/item-link-end

		*/
		public function wps_products_item_link_end($product) {

			$data = [
				'product' => $product
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/loop/item-link', 'end' );

		}


		/*

		Template: partials/products/loop/item-img

		*/
		public function wps_products_img($product) {

			$image = Images::get_image_details_from_product($product);

			$data = [
				'product' => $product,
				'image'	=> $image
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/loop/item', 'img' );

		}


		/*

		Template: partials/products/loop/item-title

		*/
		public function wps_products_title($product) {

			$data = [
				'product' => $product
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/loop/item', 'title' );

		}


		/*

		Template: partials/products/loop/item-add-to-cart

		*/
		public function wps_products_add_to_cart($product) {

			$data = [
				'product' => $product
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/loop/item-add-to', 'cart' );

		}


		/*

		Template: partials/products/loop/item-price

		*/
		public function wps_products_price($product) {

			$DB_Variants = new Variants();
			$product = Utils::wps_convert_array_to_object($product);

			/*

			Only needed because the data structure coming from the product single template is
			different from the other templates. We should standardize but need to do it in such
			a way that nothing breaks.

			*/
			if ( isset($product->details->post_id) && !isset($product->post_id) ) {
				$product->post_id = $product->details->post_id;
			}

			$variants = $DB_Variants->get_product_variants($product->post_id);


			// $variants = json_decode(json_encode($variants), true);

			// $productNew = array(
			// 	'variants' => $variants
			// );


			$amountOfVariantPrices = count($variants);

			usort($variants, function ($a, $b) {

				$result = $a->price - $b->price;

				return $result;

			});


			if ($amountOfVariantPrices > 1) {

				$lastVariantIndex = $amountOfVariantPrices - 1;
				$lastVariantPrice = $variants[$lastVariantIndex]->price;
				$firstVariantPrice = $variants[0]->price;

				if ($lastVariantPrice === $firstVariantPrice) {

					$data = [
						'price'		=> Utils::wps_format_money($firstVariantPrice, $product),
						'product' => $product
					];

					return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/add-to-cart/price', 'one' );

				} else {

					$priceFirst = Utils::wps_format_money($firstVariantPrice, $product);
					$priceLast = Utils::wps_format_money($lastVariantPrice, $product);

					$price = apply_filters('wps_products_price_multi_from', '<small class="wps-product-from-price">' . esc_html__('From: ', 'wp-shopify') . '</small>') . apply_filters('wps_products_price_multi_first', $priceFirst) . apply_filters('wps_products_price_multi_separator', ' <span class="wps-product-from-price-separator">-</span> ') . apply_filters('wps_products_price_multi_last', $priceLast);

					$data = [
						'price'				=> $price,
						'price_first' => $priceFirst,
						'price_last' 	=> $priceLast,
						'product' 		=> $product
					];

					return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/add-to-cart/price', 'multi' );

				}

			} else {

				$data = [
					'price'		=> Utils::wps_format_money($variants[0]->price, $product),
					'product' => $product
				];

				return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/add-to-cart/price', 'one' );

			}


		}


		/*

		Template: partials/products/loop/header

		*/
		public function wps_products_header($query) {

			$data = [
				'query' => $query
			];

			if ( !is_single() ) {
				return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/loop/header' );
			}

		}


		/*

		Template: partials/products/add-to-cart/meta-start

		*/
		public function wps_products_meta_start($product) {

			$data = [
				'product' 					=> $product,
				'filtered_options'	=> Utils::filter_variants_to_options_values($product->variants)
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/add-to-cart/meta', 'start' );

		}


		/*

		Template: partials/products/add-to-cart/meta-end

		*/
		public function wps_products_meta_end($product) {

			$data = [
				'product' => $product
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/add-to-cart/meta', 'end' );

		}


		/*

		Template: partials/products/add-to-cart/quantity

		*/
		public function wps_products_quantity($product) {

			$data = [
				'product' => $product
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/add-to-cart/quantity' );

		}


		/*

		Template: partials/products/action-groups/action-groups-start

		*/
		public function wps_products_actions_group_start($product) {

			$data = [
				'product' => $product
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/action-groups/start' );

		}


		/*

		Template: partials/products/add-to-cart/options

		*/
		public function wps_products_options($product) {

			if (count($product->variants) > 1) {

				if (count($product->options) === 1) {
				  $button_width = 2;

				} else {
				  $button_width = count($product->options);

				}

				$data = [
					'product' 									=> $product,
					'button_width'							=> $button_width,
					'sorted_options'						=> Utils::wps_sort_by($product->options, 'position'),
					'option_number'							=> 1,
					'variant_number'						=> 0
				];

				return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/add-to-cart/options' );

			}

		}


		/*

		Template: partials/products/add-to-cart/button-add-to-cart

		*/
		public function wps_products_button_add_to_cart($product) {

			$button_width = Utils::get_product_button_width($product);

			$data = [
				'product' 			=> $product,
				'button_width'	=> $button_width
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/add-to-cart/button-add-to', 'cart' );

		}


		/*

		Template: partials/products/action-groups/action-groups-end

		*/
		public function wps_products_actions_group_end($product) {

			$data = [
				'product' => $product
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/action-groups/end' );

		}


		/*

		Template: partials/products/add-to-cart/notice-inline

		*/
		public function wps_products_notice_inline($product) {

			$data = [
				'product' => $product
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/notices/add-to', 'cart' );

		}


		/*

		Template: partials/products/loop/no-results

		*/
		public function wps_products_no_results($args) {

			$data = [
				'args' => $args
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/notices/no', 'results' );

		}


		/*

		Template: partials/pagination/start

		*/
		public function wps_products_pagination_start() {

			$data = [];

			ob_start();
			$this->template_loader->set_template_data($data)->get_template_part( 'partials/pagination/start' );
			$output = ob_get_clean();
			return $output;

		}


		/*

		Template: partials/products/related/start

		*/
		public function wps_products_related_start() {

			$data = [];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/related/start' );

		}


		/*

		Template: partials/products/related/end

		*/
		public function wps_products_related_end() {

			$data = [];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/related/end' );

		}


		/*

		Template: partials/products/related/heading-start

		*/
		public function wps_products_related_heading() {

			$data = [];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/related/heading' );

		}


		/*

		Template: partials/pagination/end

		*/
		public function wps_products_pagination_end() {

			$data = [];

			ob_start();
			$this->template_loader->set_template_data($data)->get_template_part( 'partials/pagination/end' );
			$output = ob_get_clean();
			return $output;

		}


		/*

		Single Template for related products

		*/
		public function wps_related_products() {

			if (apply_filters('wps_products_related_show', true)) {

				if (!is_single()) {
					return;

				} else {

					$data = [];

					return $this->template_loader->set_template_data($data)->get_template_part( 'products', 'related' );

				}

			} else {
				return;

			}

		}


		/*

		Template: partials/collections/loop/loop-start

		*/
		public function wps_collections_loop_start() {

			$data = [];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/collections/loop/start' );

		}


		/*

		Template: partials/collections/loop/loop-end

		*/
		public function wps_collections_loop_end() {

			$data = [];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/collections/loop/end' );

		}


		/*

		Template: partials/collections/loop/item-start

		*/
		public function wps_collections_item_start($collection, $args, $customArgs) {

			// Shortcode will always override wps_related_products_items_per_row filter
			if (isset($customArgs['items-per-row']) && $customArgs['items-per-row'] !== false ) {
				$items_per_row = $customArgs['items-per-row'];

			} else {
				$items_per_row = apply_filters('wps_collections_items_per_row', 3);
			}

			$data = [
				'collection' 		=> $collection,
				'args' 					=> $args,
				'custom_args' 	=> $customArgs,
				'items_per_row' => $items_per_row
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/collections/loop/item', 'start' );

		}


		/*

		Template: partials/collections/loop/item-end

		*/
		public function wps_collections_item_end($collection) {

			$data = [
				'collection' 	=> $collection
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/collections/loop/item', 'end' );

		}


		/*

		Template: partials/collections/loop/item

		*/
		public function wps_collections_item($collection) {

			$data = [
				'collection' 	=> $collection
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/collections/loop/item' );

		}


		/*

		Template: partials/collections/loop/item-link-start

		*/
		public function wps_collections_item_before($collection) {

			$data = [
				'collection' 	=> $collection,
				'settings'		=> (new Config())->wps_get_settings_general()
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/collections/loop/item-link', 'start' );

		}


		/*

		Template: partials/collections/loop/item-link-end

		*/
		public function wps_collections_item_after($collection) {

			$data = [
				'collection' 	=> $collection
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/collections/loop/item-link', 'end' );

		}


		/*

		Template: partials/collections/loop/item-img

		*/
		public function wps_collections_img($collection) {

			$data = [
				'collection' 	=> $collection,
				'image'				=> Images::get_image_details_from_collection($collection)
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/collections/loop/item', 'img' );

		}


		/*

		Template: partials/collections/loop/item-title

		*/
		public function wps_collections_title($collection) {

			$data = [
				'collection' 	=> $collection
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/collections/loop/item', 'title' );

		}


		/*

		Template: partials/collections/loop/no-results

		*/
		public function wps_collections_no_results($args) {

			$data = [
				'args' => $args
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/notices/no', 'results' );

		}


		/*

		Template: partials/collections/loop/header

		*/
		public function wps_collections_header($collections) {

			$data = [
				'collections' 	=> $collections
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/collections/loop/header' );

		}


		/*

		Template: partials/products/action-groups/action-groups-start

		*/
		public function wps_product_single_actions_group_start($product) {

			$data = [
				'product' 	=> $product
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/action-groups/start' );

		}


		/*

		Template: partials/products/single/content

		*/
		public function wps_product_single_content($product) {

			if (is_object($product) && property_exists($product, 'body_html') && !empty($product->body_html) ) {

				$data = [
					'product' 	=> $product
				];

				return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/single/content' );

			} else {

				$data = [
					'type' 	=> 'product'
				];

				return $this->template_loader->set_template_data($data)->get_template_part( 'partials/notices/no', 'description' );

			}

		}


		/*

		Template: partials/products/single/header

		*/
		public function wps_product_single_header($product) {

			$data = [
				'product' 	=> $product
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/single/header' );

		}


		/*

		Template: partials/products/single/header

		*/
		public function wps_product_single_heading($product) {

			$data = [
				'product' 	=> $product
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/single/heading' );

		}


		/*

		Template: partials/products/single/imgs

		*/
		public function wps_product_single_imgs($product) {

			$product->images = Utils::sort_product_images_by_position($product->images);

			$data = [
				'product' 					=> $product,
				'settings' 					=> (new Config())->wps_get_settings_general(),
				'images'						=> $product->images,
				'index'							=> 0,
				'amount_of_thumbs'	=> count($product->images)
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/single/imgs' );

		}


		/*

		Template: partials/products/single/imgs

		*/
		public function wps_product_single_img($data, $image) {

			$data->image_type_class = 'wps-product-gallery-img-thumb';
			$data->image_details = Images::get_image_details_from_image($image, $data->product);

			if ($data->amount_of_thumbs === 1) {
				$data->amount_of_thumbs = 3;
			}

			if ($data->amount_of_thumbs > 8) {
				$data->amount_of_thumbs = 6;
			}

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/single/img' );

		}


		/*

		Template: partials/products/single/imgs-feat

		*/
		public function wps_product_single_imgs_feat_placeholder($data) {

			$data->image_type_class = 'wps-product-gallery-img-feat';

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/single/imgs-feat', 'placeholder' );

		}


		/*

		Template: partials/products/single/imgs-feat

		*/
		public function wps_product_single_imgs_feat($data, $image) {

			$image_details = Images::get_image_details_from_image($image, $data->product);
			$data->image_details = $image_details;
			$data->image_type_class = 'wps-product-gallery-img-feat';
			$data->variant_ids = Images::get_variants_from_image($image);

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/single/imgs-feat' );

		}


		/*

		Template: partials/products/single/info-start

		*/
		public function wps_product_single_info_start($product) {

			$data = [
				'product' 	=> $product
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/single/info', 'start' );

		}


		/*

		Template: partials/products/single/info-end

		*/
		public function wps_product_single_info_end($product) {

			$data = [
				'product' 	=> $product
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/single/info', 'end' );

		}


		/*

		Template: partials/products/single/gallery-start

		*/
		public function wps_product_single_gallery_start($product) {

			$data = [
				'product' 	=> $product
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/single/gallery', 'start' );

		}


		/*

		Template: partials/products/single/gallery-end

		*/
		public function wps_product_single_gallery_end($product) {

			$data = [
				'product' 	=> $product
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/single/gallery', 'end' );

		}


		/*

		Template: partials/products/single/start

		*/
		public function wps_product_single_start($product) {

			$data = [
				'product' 	=> $product
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/single/start' );

		}


		/*

		Template: partials/products/single/end

		*/
		public function wps_product_single_end($product) {

			$data = [
				'product' 	=> $product
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/single/end' );

		}


		/*

		Template: partials/notices/out-of-stock

		*/
		public function wps_products_notice_out_of_stock($product) {

			$data = [
				'product' 	=> $product
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/notices/out-of', 'stock' );

		}


		/*

		Template: partials/collections/single/start

		*/
		public function wps_collection_single_start($collection) {

			$data = [
				'collection' 	=> $collection
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/collections/single/start' );

		}


		/*

		Template: partials/collections/single/header

		*/
		public function wps_collection_single_header($collection) {

			$data = [
				'collection' 	=> $collection
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/collections/single/header' );

		}


		/*

		Template: partials/collections/single/img

		*/
		public function wps_collection_single_img($collection) {

			$data = [
				'collection' 	=> $collection,
				'image'				=> Images::get_image_details_from_collection($collection)
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/collections/single/img' );

		}


		/*

		Template: partials/collections/single/content

		*/
		public function wps_collection_single_content($collection) {

			if (is_object($collection) && property_exists($collection, 'body_html') && !empty($collection->body_html) ) {

				$data = [
					'collection' 	=> $collection
				];

				return $this->template_loader->set_template_data($data)->get_template_part( 'partials/collections/single/content' );

			} else {

				$data = [
					'type' 	=> 'collection'
				];

				return $this->template_loader->set_template_data($data)->get_template_part( 'partials/notices/no', 'description' );

			}

		}


		/*

		Template: partials/collections/single/products

		*/
		public function wps_collection_single_products($collection, $products) {

			$data = [
				'products'		=> $products,
				'collection' 	=> $collection
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/collections/single/products' );

		}


		/*

		Template: partials/collections/single/products

		*/
		public function wps_collection_single_products_heading() {

			$data = [];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/collections/single/products', 'heading' );

		}


		/*

		Template: partials/collections/single/end

		*/
		public function wps_collection_single_end($collection) {

			$data = [
				'collection' 	=> $collection
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/collections/single/end' );

		}


		/*

		Template: partials/collections/single/heading

		*/
		public function wps_collection_single_heading($collection) {

			$data = [
				'collection' 	=> $collection
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/collections/single/heading' );

		}


		/*

		Template: partials/collections/single/product

		*/
		public function wps_collection_single_product($product) {

			$data = [
				'product'		=> $product
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/collections/single/product' );

		}


		/*

		Template: partials/collections/single/products-list

		*/
		public function wps_collection_single_products_list($collection, $products) {

			if (!is_array($products) || empty($products)) {
				return $this->template_loader->get_template_part( 'partials/notices/no', 'results' );
			}

			$data = [
				'products'			=> $products,
				'collection'		=> $collection
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/collections/single/products', 'list' );

		}


		/*

		Template: partials/pagination/breadcrumbs

		*/
		public function wps_breadcrumbs($shortcodeData) {

			if (apply_filters('wps_breadcrumbs_show', false)) {

				$data = [];

				if ( empty($shortcodeData->shortcodeArgs) || empty($shortcodeData->shortcodeArgs['custom'])|| empty($shortcodeData->shortcodeArgs['custom']['breadcrumbs']) ) {

					return $this->template_loader->set_template_data($data)->get_template_part( 'partials/pagination/breadcrumbs' );

				} else {

					if (isset($shortcodeData->shortcodeArgs['custom']['breadcrumbs']) && $shortcodeData->shortcodeArgs['custom']['breadcrumbs'] === 'true') {
						return $this->template_loader->set_template_data($data)->get_template_part( 'partials/pagination/breadcrumbs' );
					}

				}

			}

		}


		/*

		Template: partials/products/single/thumbs-start

		*/
		public function wps_product_single_thumbs_start() {

			$data = [];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/single/thumbs', 'start' );

		}


		/*

		Template: partials/products/single/thumbs-end

		*/
		public function wps_product_single_thumbs_end() {

			$data = [];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/single/thumbs', 'end' );

		}


		/*

		Template: partials/cart/cart-counter

		*/
		public function wps_cart_counter() {

			$data = [];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/cart/cart', 'counter' );

		}


		/*

		Template: partials/cart/cart-icon

		*/
		public function wps_cart_icon() {

			$data = [];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/cart/cart', 'icon' );

		}


		/*

		Template - cart-button-checkout

		*/
		public function wps_cart_button_checkout() {

			$data = [
				'checkout_base_url' => WPS_CHECKOUT_BASE_URL
			];

			return $this->template_loader->set_template_data($data)->get_template_part( 'partials/cart/cart-button', 'checkout' );

		}


		/*

		Template - partials/cart/button
		Shortcode [wps_cart]

		TODO: Think about using shortcode_atts for [wps_products] and [wps_collections] as well.

		*/
		public function wps_cart_shortcode($atts) {

			$shortcode_output = '';

			// Need to cast string to proper boolean
			if (is_array($atts) && isset($atts['counter']) && $atts['counter'] === 'false') {
				$atts['counter'] = false;
			}

			$atts = shortcode_atts([
				'counter' => true
			], $atts, 'wps_cart');

			ob_start();
			$this->template_loader->set_template_data($atts)->get_template_part( 'partials/cart/cart-icon', 'wrapper' );
			$cart = ob_get_contents();
			ob_end_clean();

			$shortcode_output .= $cart;

			return $shortcode_output;

		}


		/*

		Template - partials/cart/cart

		This is slow. We should think of a better way to do this.

		*/
		public function wps_cart() {

			$DB_Settings_General = new Settings_General();
			$data = [];

			if ($DB_Settings_General->get_column_single('cart_loaded')[0]->cart_loaded) {

				ob_start();
				$this->template_loader->set_template_data($data)->get_template_part( 'partials/cart/cart' );
				$content = ob_get_contents();
				ob_end_clean();
				echo $content;

			}

		}


		/*

		Template - partials/notices/notice

		*/
		public function wps_notice() {

			$DB_Settings_General = new Settings_General();
			$data = [];

			if ($DB_Settings_General->get_column_single('cart_loaded')[0]->cart_loaded) {
				return $this->template_loader->set_template_data($data)->get_template_part( 'partials/notices/not', 'found' );
			}

		}


		/*

		Template - products-all
		Shortcode [wps_products]

		There's a few things going on here.

		1. 'wps_format_products_shortcode_args' formats the provided shortcode args
				by taking the comma seperated list of values in each attribute and constructing
				an array. It also uses the attribute name as the array key. For example"

				array(
					'title' => array(
						'Sale', 'Featured'
					)
				)''

		2. Next, it passes the array of args to 'wps_map_products_args_to_query'
			 which is the main function that constructs our custom SQL query. This is where
			 the "custom" property is set that we eventually check for within 'wps_clauses_mod'.

		3. At this point in the execution we load our template by pulling in our
			 products-all.php. This template then calls our custom action 'wps_products_display'

		4. 'wps_products_display' then calls 'wps_clauses_mod' when it invokes WP_Query.

		5. Because 'wps_clauses_mod' will get fired for both products and collections, we then
			 need to fork where the execution goes by calling one of two functions depending
			 on what we're dealing with. They are:

			 construct_clauses_from_products_shortcode
			 construct_clauses_from_collections_shortcode

			 ================================================================
			 wps_products_shortcode ->
			 wps_format_products_shortcode_args ->
			 wps_map_products_args_to_query ->
			 wps_products_display -> wps_clauses_mod (via WP_Query)
					either a. construct_clauses_from_products_shortcode
					either b. construct_clauses_from_collections_shortcode
			 ================================================================

		*/
		public function wps_products_shortcode($atts) {

			$shortcode_output = '';
			$shortcodeArgs = Utils::wps_format_products_shortcode_args($atts);

			$data = [
				'shortcodeArgs' => $shortcodeArgs,
				'is_shortcode' 	=> true
			];

			ob_start();
			$this->template_loader->set_template_data($data)->get_template_part( 'products', 'all' );
			$products = ob_get_contents();
			ob_end_clean();

			$shortcode_output .= $products;

			return $shortcode_output;

		}


		/*

		Template - collections-all
		Shortcode [wps_collections]

		*/
		public function wps_collections_shortcode($atts) {

			$shortcode_output = '';
			$shortcodeArgs = Utils::wps_format_collections_shortcode_args($atts);

			$data = [
				'shortcodeArgs' => $shortcodeArgs,
				'is_shortcode' 	=> true
			];

			ob_start();
			$this->template_loader->set_template_data($data)->get_template_part( 'collections', 'all' );
			$collections = ob_get_contents();
			ob_end_clean();

		 $shortcode_output .= $collections;

		 return $shortcode_output;

		}



		/*

		Main Template - products-single

		*/
		public function wps_single_template($template) {

			if (is_single()) {

				global $post;

				if ($post->post_type === "wps_products") {
					$template = $this->template_loader->get_template_part( 'products', 'single', false ); // passing false will return string and not load template

				} else if ($post->post_type === "wps_collections") {
					$template = $this->template_loader->get_template_part( 'collections', 'single', false );

				}

				return $template;

			}

		}


		/*

		Main Template products-all

		*/
		public function wps_all_template($template) {

			if ( is_post_type_archive('wps_products') ) {

				// Passing false will return string and not template contents
				$template = $this->template_loader->get_template_part( 'products', 'all', false );

			} else if (is_post_type_archive('wps_collections')) {
				$template = $this->template_loader->get_template_part( 'collections', 'all', false );

			}

			return $template;

		}



		public function get_shortcode_data($data) {

			if (empty($data)) {

			  $data = new \stdClass;
			  $data->shortcodeArgs = [];
			  $data->is_shortcode = false;

			} else {

			  $data->shortcodeArgs = !empty($data->shortcodeArgs) ? $data->shortcodeArgs : [];
				$data->is_shortcode = isset($data->is_shortcode) && $data->is_shortcode ? $data->is_shortcode : false;

			}

			return $data;

		}


		/*

		Show / Hide Header

		*/
		public function show_header($shortcodeData = false) {

			if (empty($shortcodeData) || empty($shortcodeData->is_shortcode)) {
				get_header('wps');
			}

		}


		/*

		Show / Hide Footer

		*/
		public function show_footer($shortcodeData = false) {

			if (empty($shortcodeData) || empty($shortcodeData->is_shortcode)) {
				get_footer('wps');
			}

		}


		/*

		Initilizing Templates

		*/
		public function init() {


			/*

			Shortcodes

			*/
			add_shortcode('wps_products', [$this, 'wps_products_shortcode']);
			add_shortcode('wps_collections', [$this, 'wps_collections_shortcode']);
			add_shortcode('wps_cart', [$this, 'wps_cart_shortcode']);


			/*

			Cart & Breadcrumbs

			*/
			add_action('wps_breadcrumbs', [$this, 'wps_breadcrumbs']);
			add_action('wp_footer', [$this, 'wps_notice']);
			add_action('wp_footer', [$this, 'wps_cart']);
			add_action('wps_cart_icon', [$this, 'wps_cart_icon']);
			add_action('wps_cart_counter', [$this, 'wps_cart_counter']);
			add_action('wps_cart_checkout_btn', [$this, 'wps_cart_button_checkout']);


			/*

			Main Templates

			*/
			add_filter('single_template', [$this, 'wps_single_template']);
			add_filter('archive_template', [$this, 'wps_all_template']);


			/*

			Products & Collections

			*/
			add_action('wps_collections_header', [$this, 'wps_collections_header']);
			add_action('wps_collections_loop_start', [$this, 'wps_collections_loop_start']);
			add_action('wps_collections_loop_end', [$this, 'wps_collections_loop_end']);
			add_action('wps_collections_item_start', [$this, 'wps_collections_item_start'], 10, 3);
			add_action('wps_collections_item_end', [$this, 'wps_collections_item_end']);
			add_action('wps_collections_item', [$this, 'wps_collections_item']);
			add_action('wps_collections_item_before', [$this, 'wps_collections_item_before']);
			add_action('wps_collections_item_after', [$this, 'wps_collections_item_after']);
			add_action('wps_collections_img', [$this, 'wps_collections_img']);
			add_action('wps_collections_title', [$this, 'wps_collections_title']);
			add_action('wps_collections_no_results', [$this, 'wps_collections_no_results']);

			add_action('wps_collection_single_start', [$this, 'wps_collection_single_start']);
			add_action('wps_collection_single_header', [$this, 'wps_collection_single_header']);
			add_action('wps_collection_single_img', [$this, 'wps_collection_single_img']);
			add_action('wps_collection_single_content', [$this, 'wps_collection_single_content']);
			add_action('wps_collection_single_products', [$this, 'wps_collection_single_products'],  10, 3);
			add_action('wps_collection_single_products_list', [$this, 'wps_collection_single_products_list'], 10, 3);
			add_action('wps_collection_single_products_heading', [$this, 'wps_collection_single_products_heading']);
			add_action('wps_collection_single_end', [$this, 'wps_collection_single_end']);
			add_action('wps_collection_single_product', [$this, 'wps_collection_single_product']);
			add_action('wps_collection_single_heading', [$this, 'wps_collection_single_heading'], 10);

			add_action('wps_products_header', [$this, 'wps_products_header']);
			add_action('wps_products_loop_start', [$this, 'wps_products_loop_start']);
			add_action('wps_products_loop_end', [$this, 'wps_products_loop_end']);
			add_action('wps_products_item_start', [$this, 'wps_products_item_start'], 10, 3);
			add_action('wps_products_item_end', [$this, 'wps_products_item_end']);
			add_action('wps_products_item', [$this, 'wps_products_item'], 10, 3);
			add_action('wps_products_item_link_start', [$this, 'wps_products_item_link_start'], 10, 2);
			add_action('wps_products_item_link_end', [$this, 'wps_products_item_link_end']);
			add_action('wps_products_img', [$this, 'wps_products_img']);
			add_action('wps_products_title', [$this, 'wps_products_title']);
			add_action('wps_products_price', [$this, 'wps_products_price']);
			add_action('wps_products_no_results', [$this, 'wps_products_no_results']);
			add_action('wps_products_add_to_cart', [$this, 'wps_products_add_to_cart']);
			add_action('wps_products_meta_start', [$this, 'wps_products_meta_start']);
			add_action('wps_products_quantity', [$this, 'wps_products_quantity']);
			add_action('wps_products_options', [$this, 'wps_products_options']);
			add_action('wps_products_button_add_to_cart', [$this, 'wps_products_button_add_to_cart']);
			add_action('wps_products_actions_group_start', [$this, 'wps_products_actions_group_start']);
			add_action('wps_products_actions_group_end', [$this, 'wps_products_actions_group_end']);
			add_action('wps_products_notice_inline', [$this, 'wps_products_notice_inline']);
			add_action('wps_products_meta_end', [$this, 'wps_products_meta_end']);
			add_action('wps_products_related_start', [$this, 'wps_products_related_start']);
			add_action('wps_products_related_end', [$this, 'wps_products_related_end']);
			add_action('wps_products_related_heading', [$this, 'wps_products_related_heading']);
			add_action('wps_products_notice_out_of_stock', [$this, 'wps_products_notice_out_of_stock']);
			add_filter('wps_products_pagination_start', [$this, 'wps_products_pagination_start']);
			add_filter('wps_products_pagination_end', [$this, 'wps_products_pagination_end']);

			add_action('wps_product_single_after', [$this, 'wps_related_products']);


			add_action('wps_product_single_actions_group_start', [$this, 'wps_product_single_actions_group_start']);
			add_action('wps_product_single_content', [$this, 'wps_product_single_content']);

			add_action('wps_product_single_header', [$this, 'wps_product_single_header']);
			add_action('wps_product_single_heading', [$this, 'wps_product_single_heading']);

			add_action('wps_product_single_img', [$this, 'wps_product_single_img'], 10, 2);
			add_action('wps_product_single_imgs', [$this, 'wps_product_single_imgs']);
			add_action('wps_product_single_imgs_feat_placeholder', [$this, 'wps_product_single_imgs_feat_placeholder']);
			add_action('wps_product_single_imgs_feat', [$this, 'wps_product_single_imgs_feat'], 10, 2);

			add_action('wps_product_single_info_start', [$this, 'wps_product_single_info_start']);
			add_action('wps_product_single_info_end', [$this, 'wps_product_single_info_end']);
			add_action('wps_product_single_gallery_start', [$this, 'wps_product_single_gallery_start']);
			add_action('wps_product_single_gallery_end', [$this, 'wps_product_single_gallery_end']);
			add_action('wps_product_single_start', [$this, 'wps_product_single_start']);
			add_action('wps_product_single_end', [$this, 'wps_product_single_end']);
			add_action('wps_product_single_thumbs_start', [$this, 'wps_product_single_thumbs_start']);
			add_action('wps_product_single_thumbs_end', [$this, 'wps_product_single_thumbs_end']);


		}

	}

}

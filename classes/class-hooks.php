<?php

namespace WPS;

use WPS\Utils;
use WPS\DB;
use WPS\Transients;
use WPS\Template_Loader;

use WPS\DB\Products;
use WPS\DB\Variants;
use WPS\DB\Collections_Smart;
use WPS\DB\Collections_Custom;
use WPS\DB\Settings_General;

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}


/*

Hooks Class

*/
if (!class_exists('Hooks')) {

	class Hooks {

		protected static $instantiated = null;
		private $Config;

    /*

    Initialize the class and set its properties.

    */
    public function __construct($Config) {
      $this->config = $Config;
			$this->template_loader = new Template_Loader;
    }


		/*

		Creates a new class if one hasn't already been created.
		Ensures only one instance is used.

		*/
		public static function instance($Config) {

			if (is_null(self::$instantiated)) {
				self::$instantiated = new self($Config);
			}

			return self::$instantiated;

		}


    /*

    Products Pagination

    */
		public function wps_products_pagination($productsQuery) {

			$Utils = new Utils();

			$args = [
				'query' => $productsQuery
			];

			// If user turns pagination off via shortcode just exit
			if (isset($args['query']->query['custom']['pagination'])) {
				return;
			}

			if (isset($args['query']->query['paged']) && $args['query']->query['paged'] ) {
				echo $Utils->wps_get_paginated_numbers($args);
			}

		}


		/*

    Collections Pagination

    */
		public function wps_collections_pagination($collectionsQuery) {

			$Utils = new Utils();
			$args = array(
				'query' => $collectionsQuery
			);


			// If user turns pagination off via shortcode just exit
			if (isset($args['query']->query['custom']['pagination'])) {
				return;
			}

			if (isset($args['query']->query['paged']) && $args['query']->query['paged'] ) {
				echo $Utils->wps_get_paginated_numbers($args);
			}
			

		}


		/*

		wps_products_custom_args

		*/
		public function wps_products_custom_args($args) {

			return array(
				'items_per_row' => apply_filters('wps_products_custom_args_items_per_row', 3)
			);

		}

		public function wps_collections_custom_args() {

			return array(
				'items_per_row' => apply_filters('wps_collections_custom_args_items_per_row', 3)
			);

		}

		public function wps_products_related_custom_args() {

			return array(
				'items_per_row' => apply_filters('wps_products_related_custom_items_per_row', 4)
			);

		}

		public function wps_products_custom_args_items_per_row($items_per_row) {
			return 3;
		}

		public function wps_collections_custom_args_items_per_row($items_per_row) {
			return 4;
		}

		public function wps_products_related_custom_items_per_row($items_per_row) {
			return 4;
		}

		public function wps_products_pagination_first_page_text() {
			return 'First';
		}

		public function wps_products_pagination_next_link_text() {
			return '';
		}

		public function wps_products_pagination_prev_link_text() {
			return '';
		}

		public function wps_products_pagination_prev_page_text() {
			return '<<';
		}

		public function wps_products_pagination_next_page_text() {
			return '>>';
		}

		public function wps_products_pagination_show_as_prev_next() {
			return false;
		}

		public function wps_products_pagination_range() {
			return 5;
		}

		public function wps_collection_single_heading_before($collection) {
			echo '';
		}

		public function wps_collection_single_heading_after($collection) {
			echo '';
		}

		public function wps_product_single_thumbs_class() {
			return;
		}

		public function wps_products_related_before() {
			echo '';
		}

		public function wps_products_related_after() {
			echo '';
		}

		public function wps_products_related_heading_text() {
			echo 'Related';
		}

		public function wps_products_related_heading_end_after() {
			echo '';
		}

		public function wps_collection_single_products_heading_class() {
			return '';
		}

		public function wps_collections_heading_class($collections) {
			return '';
		}

		public function wps_collections_heading($collections) {
			return '';
		}

		public function wps_products_title_class() {
			return '';
		}

		public function wps_collections_title_class() {
			return '';
		}
		public function wps_collections_img_class() {
			return '';
		}

		public function wps_products_img_class() {
			return '';
		}

		public function wps_collections_link_class() {
			return '';
		}

		public function wps_products_link_class() {
			return '';
		}

		public function wps_product_class() {
			return '';
		}
		public function wps_products_class() {
			return '';
		}

		public function wps_collections_class() {
			return '';
		}

		public function wps_collection_class() {
			return '';
		}

		public function wps_products_heading_class() {
			return '';
		}

		public function wps_collection_single_products_heading() {
			return 'Products';
		}

		public function wps_cart_before() {
			echo '';
		}

		public function wps_cart_after() {
			echo '';
		}

		public function wps_cart_title_text() {
			return 'Shopping cart';
		}

		public function wps_cart_close_icon() {
			return '&times;';
		}

		public function wps_cart_total_text() {
			return 'Total';
		}

		public function wps_cart_checkout_text() {
			return 'Checkout';
		}

		public function wps_cart_shipping_text() {
			return '';
		}

		public function wps_cart_item_class() {
			return '';
		}

		public function wps_cart_class() {
			return '';
		}

		public function wps_cart_counter_class() {
			return '';
		}

		public function wps_cart_icon_class() {
			return '';
		}

		public function wps_cart_btn_class() {
			return '';
		}

		public function wps_collections_after($collections) {
			echo '';
		}

		public function wps_products_related_args_posts_per_page($posts_per_page) {
			return $posts_per_page;
		}

		public function wps_products_related_args_orderby($orderby) {
			return $orderby;
		}

		public function wps_products_price_multi($defaultPrice, $priceFirst, $priceLast, $product) {
			return $defaultPrice;
		}

		public function wps_products_price_one($defaultPrice, $product) {
			return $defaultPrice;
		}

		public function wps_product_single_price_multi($defaultPrice, $priceFirst, $priceLast, $product) {
			return $defaultPrice;
		}

		public function wps_product_single_price_one($defaultPrice, $finalPrice, $product) {
			return $defaultPrice;
		}

		public function wps_products_args_posts_per_page($posts_per_page) {
			return $posts_per_page;
		}

		public function wps_products_args_orderby($orderby) {
			return $orderby;
		}

		public function wps_products_args_paged($paged) {
			return $paged;
		}


		/*

		wps_products_join

		*/
		public function wps_products_join($sql, $query) {

			global $wpdb;

			$DB_Products = new Products();
			$DB_Variants = new Variants();

	    $table_products = $DB_Products->get_table_name();
			$table_variants = $DB_Variants->get_table_name();


			if ($query->get('context') === 'wps_products_query') {

				/*

				User passed in custom shortcode filtering options

				*/
				if ($query->get('custom')) {

					$sql = Utils::construct_join_from_products_shortcode($query->get('custom'));


				} else {

					$sql .= " INNER JOIN $table_products ON " .
						 $wpdb->posts . ".ID = " . $table_products .
						 ".post_id ";

					$sql .= " INNER JOIN " . $table_variants . " ON " .
						 $table_products . ".product_id = " . $table_variants .
						 ".product_id AND " . $table_variants . ".position = 1";

				}

			} else if ($query->get('context') === 'wps_collections_query') {

				$DB_Collections_Custom = new Collections_Custom();
				$table_collections_custom = $DB_Collections_Custom->get_table_name();

				$DB_Collections_Smart = new Collections_Smart();
				$table_collections_smart = $DB_Collections_Smart->get_table_name();

				$sql .= " INNER JOIN " . $table_collections_custom . " ON " .
					 $wpdb->posts . ".ID = " . $table_collections_custom .
					 ".post_id ";

 				$sql .= " INNER JOIN " . $table_collections_smart . " ON " .
 				 $wpdb->posts . ".ID = " . $table_collections_smart .
 				 ".post_id ";

			}

			return $sql;

		}


		/*

		wps_clauses_mod

		*/
		public function wps_clauses_mod($clauses, $query) {

			if (!is_admin()) {

				global $wpdb;
				global $post;

				$DB_Products = new Products();
				$DB_Collections_Smart = new Collections_Smart();
				$DB_Collections_Custom = new Collections_Custom();
				$DB = new DB();



				if ($query->get('context') === 'wps_products_query') {

					// If using Shortcode or related products ...
					if ($query->get('custom')) {

						$clauses = Utils::construct_clauses_from_products_shortcode($query->get('custom'), $query);


					} else {

						$clauses = $DB_Products->get_default_query();

					}

				} else if ($query->get('context') === 'wps_collections_query') {

					// If Shortcode has attributes passed in ...
					if ($query->get('custom')) {
						$clauses = Utils::construct_clauses_from_collections_shortcode($query->get('custom'), $query);

					} else {

						$clauses = $DB->get_default_collections_query($clauses);

					}

				}


				// TODO: Revisit, make better
				if ($query->get('context') === 'wps_products_query' || $query->get('context') === 'wps_collections_query') {

					if (empty($clauses['limits'])) {

						/*

						This check is needed so as not to override any additional loops on the page.
						TODO: Do research to ensure more additional loops aren't affected

						*/
						if (isset($post->post_content)) {
						  $content = $post->post_content;

						  if( has_shortcode( $content, 'wps_products' ) || has_shortcode( $content, 'wps_collections' ) ) {
								// $clauses['limits'] = Utils::construct_pagination_limits($query);
						  }

						}

						$clauses['limits'] = Utils::construct_pagination_limits($query);

					}

				}

			}


			/*

			This forces the related products to _show_ in random order.
			TODO: Need the ability to allow customers to change.

			*/
			if ($query->get('wps_related_products')) {

				$customFilters = $query->get('custom');

				/*

				All we're doing here is adding the order and orderby values to the query again.
				Since wps_clauses_mod runs last, it will override our previous settings.

				TODO: We should able to restrucutre this so we don't have add it twice

				*/
				if (isset($customFilters['orderby']) && !empty($customFilters['orderby'])) {

					if ($customFilters['orderby'] === 'price') {
						$customFilters['orderby'] = 'variants.price';
					}

					if (isset($customFilters['order']) && $customFilters['order']) {
						$customFilters['orderby'] = $customFilters['orderby'] . ' ' . $customFilters['order'];
					}

					$clauses['orderby'] = $customFilters['orderby'];


				} else {

					// If the user didn't set a custom orderby then use random by default
					$clauses['orderby'] = 'RAND()';
				}


			}


			return $clauses;

		}


		/*

		Sidebar: Collections Single

		*/
		public function wps_collection_single_sidebar() {

			$sidebar = apply_filters('wps_collection_single_show_sidebar', false);

			if ($sidebar) {
				get_sidebar('wps');
			}

		}


		/*

		Sidebar: Collections

		*/
		public function wps_collections_sidebar() {

			$sidebar = apply_filters('wps_collections_show_sidebar', false);

			if ($sidebar) {
				get_sidebar('wps');
			}

		}


		/*

		Sidebar: Products Single

		*/
		public function wps_product_single_sidebar() {

			$sidebar = apply_filters('wps_product_single_show_sidebar', false);

			if ($sidebar) {
				get_sidebar('wps');
			}

		}


		/*

		Sidebar: Products

		*/
		public function wps_products_sidebar() {

			$showSidebar = apply_filters('wps_products_show_sidebar', false);

			if ($showSidebar) {
				get_sidebar('wps');
			}

		}


		/*

		Products Display Wrapper

		*/
		public function wps_products_display($args, $customArgs) {

			if (!is_admin()) {

				global $wpdb;

				$args['context'] = 'wps_products_query';

				if (is_single()) {
					$args['is_single'] = true;

				} else {
					$args['is_single'] = false;

				}


				$productQueryHash = md5(serialize($args));

				/*

				Here we're caching an entire WP_Query response by hashing the
				argument array. We can safely assume that a given set of args
				will always produce the same list of products if the
				product data doesn't change.

				Therefore it's important that we clear this cache whenever a
				product is updated, created, or deleted. OR whenever the plugin
				settings are updated.

				*/
		    if (get_transient('wps_products_query_hash_cache_' . $productQueryHash)) {
		      $productsQuery = get_transient('wps_products_query_hash_cache_' . $productQueryHash);

		    } else {

					$productsQuery = new \WP_Query($args);


					if ( isset($args['orderby']) ) {
						$custom_order_by = $args['orderby'];

					} else if (isset($args['custom']['orderby'])) {
						$custom_order_by = $args['custom']['orderby'];

					} else {
						$custom_order_by = false;
					}


					if ( $custom_order_by !== 'rand' ) {
						set_transient('wps_products_query_hash_cache_' . $productQueryHash, $productsQuery);
					}

		    }


				if (Utils::wps_is_manually_sorted($args)) {
					$wps_products = Utils::wps_manually_sort_posts_by_title($args['custom']['titles'], $productsQuery->posts);

				} else {
					$wps_products = $productsQuery->posts;
				}


				// Adding feature imaged to object
				foreach ($wps_products as $wps_product) {
		      $wps_product->feat_image = Utils::get_feat_image_by_id($wps_product->post_id);
		    }



				/*

				Show add to cart button if add to cart is passed in

				*/
				if (isset($args['custom']['add-to-cart']) && $args['custom']['add-to-cart']) {
					add_filter( 'wps_products_show_add_to_cart', function() { return true; });
				}


				$data = [
					'query'								=>	$productsQuery,
					'args'								=>	Utils::wps_convert_array_to_object($args),
					'custom_args'					=>	isset($args['custom']) ? $args['custom'] : [],
					'amount_of_products'	=>	count($wps_products),
					'products'						=>	$wps_products,
					'settings'						=>  $this->config->wps_get_settings_general()
				];

				return $this->template_loader->set_template_data($data)->get_template_part( 'partials/products/all', 'display' );


			}

		}


		/*

		Collections Display Wrapper
		TODO: Combine with wps_products_display?

		Fires the wps_clauses_mod during WP_Query

		*/
		public function wps_collections_display($args, $customArgs) {

			if (!is_admin()) {

				$args['context'] = 'wps_collections_query';

				if (is_single()) {
					$args['is_single'] = true;

				} else {
					$args['is_single'] = false;

				}

				$collectionsQueryHash = md5(serialize($args));

				/*

				Here we're caching an entire WP_Query response by hashing the
				argument array. We can safely assume that a given set of args
				will always produce the same list of products assuming the
				product data doesn't change. Therefore it's important that we clear
				this cache whenever a product is updated, created, or deleted.

				*/
				if (get_transient('wps_collections_query_hash_cache_' . $collectionsQueryHash)) {
					$collectionsQuery = get_transient('wps_collections_query_hash_cache_' . $collectionsQueryHash);

				} else {

					$collectionsQuery = new \WP_Query($args);
					set_transient('wps_collections_query_hash_cache_' . $collectionsQueryHash, $collectionsQuery);

				}


				if (Utils::wps_is_manually_sorted($args)) {
					$collections = Utils::wps_manually_sort_posts_by_title($args['custom']['titles'], $collectionsQuery->posts);


				} else {
					$collections = $collectionsQuery->posts;

				}


				// Adding feature imaged to object
				foreach ($collections as $collection) {
					$collection->feat_image = Utils::get_feat_image_by_id($collection->post_id);
				}



				$data = [
					'query'									=>	$collectionsQuery,
					'args'									=>	Utils::wps_convert_array_to_object($args),
					'custom_args'						=>	isset($args['custom']) ? $args['custom'] : [],
					'amount_of_collections'	=>	count($collections),
					'collections'						=>	$collections,
					'settings'							=>  $this->config->wps_get_settings_general()
				];

				return $this->template_loader->set_template_data($data)->get_template_part( 'partials/collections/all', 'display' );


			}

		}


		/*

		Related Products Config

		*/
		public function wps_products_related_args($defaultArgs) {

			global $post;
			$DB_Products = new Products();

			return array(
				'post_type' 										=> $post->post_type,
        'post_status' 									=> 'publish',

				// Not currently used
        'posts_per_page' 								=> apply_filters('wps_products_related_args_posts_per_page', 4),
				'orderby'   										=> apply_filters('wps_products_related_args_orderby', 'rand'),
        'paged' 												=> false,
				'post__not_in' 									=> array($post->ID),
				'wps_related_products' 					=> true,

				// Allows for custom filtering of related products
				'custom' 												=> apply_filters('wps_products_related_filters', [], $DB_Products->get_data($post->ID)),

				// Allows for customing how many related products show
				'wps_related_products_count' 		=> apply_filters('wps_products_related_args_posts_per_page', 4),

				// Allows for customing how many related products per row
				'wps_related_products_items_per_row' => apply_filters('wps_products_related_args_items_per_row', false)

			);

		}


		/*

		Main Collections Config
		TODO: Think about combining with wps_products_args

		*/
		public function wps_collections_args($shortcodeData) {

			$DB_Settings_General = new Settings_General();
			$settingsNumPosts = $DB_Settings_General->get_num_posts();

			$paged = get_query_var('paged') ? get_query_var('paged') : 1;


			if ( empty($shortcodeData->shortcodeArgs) ) {

				return [
					'post_type' 			=> 'wps_collections',
					'post_status' 		=> 'publish',
					'posts_per_page' 	=> apply_filters('wps_collections_args_posts_per_page', $settingsNumPosts),
					'orderby'   			=> apply_filters('wps_collections_args_orderby', 'desc'),
					'paged' 					=> apply_filters('wps_collections_args_paged', $paged)
				];

			} else {

				$shortcodeData->shortcodeArgs['paged'] = $paged;
				return $shortcodeData->shortcodeArgs;

			}

		}


		/*

		Main Products Config

		*/
		public function wps_products_args($shortcodeData) {

			$DB_Settings_General = new Settings_General();
			$settingsNumPosts = $DB_Settings_General->get_num_posts();

			$paged = get_query_var('paged') ? get_query_var('paged') : 1;

			if ( empty($shortcodeData->shortcodeArgs) ) {

				return [
					'post_type' => 'wps_products',
					'post_status' => 'publish',
					'posts_per_page' => apply_filters('wps_products_args_posts_per_page', $settingsNumPosts),
					'orderby'   => apply_filters('wps_products_args_orderby', 'desc'),
					'paged' => apply_filters('wps_products_args_paged', $paged)
				];

			} else {

				$shortcodeData->shortcodeArgs['paged'] = $paged;
				return $shortcodeData->shortcodeArgs;

			}

		}


		/*

		Need to get pagination to work

		*/
		public function wps_content_pre_loop($query) {

			/*

			Ensures our mods will only run during our custom queries

			*/
			if ( is_admin() || $query->get('post_type') !== 'wps_products' && $query->get('post_type') !== 'wps_collections' ) {
				return;
			}


			/*

			Improves performance of related products query.
			More here -- https://kinsta.com/blog/wp-query/

			*/
			if ($query->get('wps_related_products')) {
				$query->set( 'category_name', 'wp-shopify' );
				$query->set( 'no_found_rows', true );
				$query->set( 'update_post_meta_cache', false );
				$query->set( 'update_post_term_cache', false );
			}


			$DB_Settings_General = new Settings_General();
			$query->set('posts_per_page', $DB_Settings_General->get_num_posts());


			return $query;

		}


		/*

		Product single price

		*/
		public function wps_product_single_price($default, $priceFirst, $priceLast, $product) {

			$finalPrice = '';

			if ($priceFirst !== $priceLast) {
				$defaultPrice = apply_filters('wps_product_single_price_multi_from', '<small class="wps-product-from-price">From: </small>') . apply_filters('wps_product_single_price_multi_first', $priceFirst) . apply_filters('wps_product_single_price_multi_separator', ' <span class="wps-product-from-price-separator">-</span> ') . apply_filters('wps_product_single_price_multi_last', $priceLast);

				$finalPrice = apply_filters('wps_product_single_price_multi', $defaultPrice, $priceFirst, $priceLast, $product);

			} else {

				$finalPrice = apply_filters('wps_product_single_price_one', $priceFirst, $priceFirst, $product);

			}

			return $finalPrice;

		}


		/*

		Fired on plugin update

		*/
		public function wps_on_update() {

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			$DB_Settings_General = new Settings_General();
			$currentPluginVersion = $this->config->get_current_plugin_version();
			$newPluginVersion = $this->config->get_new_plugin_version();
			$generalSettings = $DB_Settings_General->get_column_single('id');

			/*

			This will run once the plugin updates. It will only run once since we're
			updating the plugin verison after everything gets executed.

			If current version is behind new version

			*/

			if (version_compare($currentPluginVersion, $newPluginVersion, '<')) {

				global $wpdb;

				// First delete necessary cache
				$DB_Transients = new Transients();
				$DB_Transients->delete_cached_product_single();
				$DB_Transients->delete_cached_product_queries();
				$DB_Transients->delete_cached_collection_queries();
				$DB_Transients->delete_cached_variants();
				$DB_Transients->delete_cached_prices();

				// Next get all tables
				$DB = new DB();
				$tables = $DB->get_table_delta();

				if (is_array($tables) && !empty($tables)) {

					foreach ($tables as $table) {
						\dbDelta( $table->create_table_query() );
					}

				}


				if ( !empty($generalSettings) ) {

					// Now update plugin version to latest
					$DB_Settings_General->update_column_single(
						array('plugin_version' => $newPluginVersion),
						array('id' => $generalSettings[0]->id)
					);

				}


			}

		}

	}

}

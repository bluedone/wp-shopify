<?php

namespace WPS;

use WPS\Utils;

if (!defined('ABSPATH')) {
	exit;
}


class Async_Processing_Posts_Products_Relationships extends Vendor_Background_Process {

	protected $action = 'wps_background_processing_posts_prod_r';

	protected $DB_Products;
	protected $DB_Settings_Syncing;
	protected $DB_Tags;

	public function __construct($DB_Products, $DB_Settings_Syncing, $DB_Tags) {

		$this->DB_Products 											= $DB_Products;
		$this->DB_Settings_Syncing 							= $DB_Settings_Syncing;
		$this->DB_Tags 													= $DB_Tags;

		parent::__construct();

	}


	protected function add_product_id_to_posts($posts) {

		$product_data = [];

		foreach ($posts as $post) {

			$product = $this->DB_Products->get_product_from_post_name($post->post_name);

			if (!empty($product)) {
				$product_data[$post->ID]['product_id'] = $product->product_id;
				$product_data[$post->ID]['post_id'] = $post->ID;
			}

		}

		return $product_data;

	}


	/*

	Update post meta

	*/
	protected function update_products_post_meta($post) {
		return $this->DB_Products->update_post_meta_helper($post['post_id'], WPS_PRODUCTS_LOOKUP_KEY, $post[WPS_PRODUCTS_LOOKUP_KEY]);
	}


	protected function update_products_table($post) {

		return $this->DB_Products->update_column_single(['post_id' => $post['post_id']], [WPS_PRODUCTS_LOOKUP_KEY => $post[WPS_PRODUCTS_LOOKUP_KEY]]);

	}


	protected function update_tags_table($post) {

		return $this->DB_Tags->update_column_single(['post_id' => $post['post_id']], [WPS_PRODUCTS_LOOKUP_KEY => $post[WPS_PRODUCTS_LOOKUP_KEY]]);

	}


	protected function has_error($result, $post) {

		if (is_wp_error($result)) {

			$existing_value = get_post_meta($post['post_id'], WPS_PRODUCTS_LOOKUP_KEY, true);

			if ($existing_value !== $post[WPS_PRODUCTS_LOOKUP_KEY]) {

				$this->DB_Settings_Syncing->save_notice($result);
				$this->complete();
				return true;

			} else {
				return false;
			}

		} else {
			return false;
		}

	}


	/*

	Override this method to perform any actions required during the async request.

	*/
	protected function task($posts) {

		$insertion_results = [];

		$product_data = $this->add_product_id_to_posts($posts);


		foreach ($product_data as $post) {

			$update_post_meta_result = $this->update_products_post_meta($post);

			if ($this->has_error($update_post_meta_result, $post)) {
				return false;
			}

			$products_table_updated = $this->update_products_table($post);

			if ($this->has_error($products_table_updated, $post)) {
				return false;
			}

			$tags_table_updated = $this->update_tags_table($post);

			if ($this->has_error($tags_table_updated, $post)) {
				return false;
			}


			$insertion_results[$post['post_id']]['post_meta'] = $update_post_meta_result;
			$insertion_results[$post['post_id']]['product_post_id'] = $products_table_updated;
			$insertion_results[$post['post_id']]['tags_table_'] = $tags_table_updated;

		}

		return false;


	}



	public function insert_posts_products_relationships($posts) {

		if ( $this->DB_Products->max_packet_size_reached($posts) ) {

			// $posts_chunked = array_chunk($posts, 50);
			//
			// foreach ($posts_chunked as $chunk) {
			// 	$this->push_to_queue($chunk);
			// 	$this->save();
			//
			// }
			//
			// $this->dispatch();

			$this->DB_Settings_Syncing->save_notice_and_stop_sync( $this->DB_Settings_Syncing->throw_max_allowed_packet() );

			$this->DB_Settings_Syncing->expire_sync();
			$this->complete();

		}


		$this->push_to_queue($posts);
		$this->save()->dispatch();


	}


	/*

	When the background process completes ...

	*/
	protected function complete() {

		$this->DB_Settings_Syncing->set_finished_product_posts_relationships(1);
		parent::complete();

	}

}

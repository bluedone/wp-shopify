<?php

namespace WPS;

use WPS\CPT;

if (!defined('ABSPATH')) {
	exit;
}

if ( !class_exists('Async_Processing_Posts_Collections_Smart') ) {

  class Async_Processing_Posts_Collections_Smart extends WP_Shopify_Background_Process {

		protected $action = 'wps_background_processing_posts_collections_smart';

		protected $DB_Settings_Syncing;
		protected $WS;
		protected $CPT_Query;
		protected $DB_Collections_Smart;

		public function __construct($DB_Settings_Syncing, $WS, $CPT_Query, $DB_Collections_Smart) {

			global $wpdb;

			$this->wpdb 											= $wpdb;
			$this->DB_Settings_Syncing 				= $DB_Settings_Syncing;
			$this->WS 												= $WS;
			$this->CPT_Query 									= $CPT_Query;
			$this->DB_Collections_Smart 			= $DB_Collections_Smart;

			parent::__construct();

		}


		/*

		Override this method to perform any actions required during the async request.

		*/
		protected function task($collections_from_shopify) {

			// Stops background process if syncing stops
			if ( !$this->DB_Settings_Syncing->is_syncing() ) {
				$this->complete();
				return false;
			}


			global $wpdb;

			/*

			Step 1. WORKING

			First, we need to determine whether any posts of type 'wps_collections' exist or not.
			Even if only one post out of 40,000 exists we'll need to know.

			If empty, we know this is an initial sync. Therefore we don't need to find the post
			IDs prior to inserting. Simply INSERT. If not empy, we need to find these IDs so that
			we can then perform an update + insert.

			*/
			if ( !CPT::collections_posts_exist() ) {

				// INSERT only
				$insert_query = $this->CPT_Query->construct_posts_insert_query($collections_from_shopify, false, WPS_COLLECTIONS_POST_TYPE_SLUG);

				/*

				Use the identity operator (===) to check for errors (e.g., false === $result),
				and whether any rows were affected (e.g., 0 === $result).

				*/
				$result = $this->CPT_Query->query($insert_query, 'smart_collections');

				if (is_wp_error($result)) {
					$this->WS->save_notice_and_stop_sync($result);
					$this->complete();
					return false;
				}



			} else {

				$total_collections_posts = CPT::num_of_posts(WPS_COLLECTIONS_POST_TYPE_SLUG);

				$total_collections_to_sync = $this->DB_Settings_Syncing->syncing_totals_custom_collections_actual();
				$total_collections_to_sync += $this->DB_Settings_Syncing->syncing_totals_smart_collections_actual();


				/*

				Step 2. Find the current post IDs and post_name (slugs)

				*/
				$existing_collections = CPT::truncate_post_data( CPT::get_all_posts(WPS_COLLECTIONS_POST_TYPE_SLUG) );


				/*

				Step 3. Now we need to filter the list of collections from the DB down to only
				reflect the current batch. We do this by filtering the array by the post name

				*/

				$collections_to_update = $this->CPT_Query->find_posts_to_update($collections_from_shopify, $existing_collections);



				/*

				Step 3.

				Three scenarios could exists:
					a. Zero posts exist 															-- INSERT only
					b. less posts than data exist (new collections)		-- Both UPDATE and INSERT only
					c. the same amount of posts and data exists				-- UPDATE only

				*/


				// The same amount of posts and Shopify collections exists
				if ($total_collections_posts === $total_collections_to_sync) {

					$stuff = $this->CPT_Query->format_posts_for_update($collections_to_update, WPS_COLLECTIONS_POST_TYPE_SLUG);
					$final_update_query = $this->CPT_Query->construct_posts_update_query($stuff);

					$result = $this->CPT_Query->query($final_update_query, 'smart_collections');

					if (is_wp_error($result)) {
						$this->WS->save_notice_and_stop_sync($result);
						$this->complete();
						return false;
					}


				} else {

					$insert_query = $this->CPT_Query->construct_posts_insert_query($collections_from_shopify, $existing_collections, WPS_COLLECTIONS_POST_TYPE_SLUG);
					$result_insert = $this->CPT_Query->query($insert_query, 'smart_collections');


					if (is_wp_error($result_insert)) {
						$this->WS->save_notice_and_stop_sync($result_insert);
						$this->complete();
						return false;
					}



					$posts_to_update = $this->CPT_Query->format_posts_for_update($collections_to_update, WPS_COLLECTIONS_POST_TYPE_SLUG);
					$final_update_query = $this->CPT_Query->construct_posts_update_query($posts_to_update);


					$result_update = $this->CPT_Query->query($final_update_query, 'smart_collections');

					if (is_wp_error($result_update)) {
						$this->WS->save_notice_and_stop_sync($result_update);
						$this->complete();
						return false;
					}

				}

			}

			return false;

		}


		protected function after_queue_item_removal($collections_from_shopify) {
			$this->DB_Settings_Syncing->increment_current_amount('smart_collections', count($collections_from_shopify));
		}


		public function insert_posts_collections_smart_batch($collections) {

			$this->push_to_queue($collections);
			$this->save()->dispatch();

		}


		/*

		When the background process completes ...

		*/
		protected function complete() {

			if (!$this->DB_Settings_Syncing->is_syncing() || $this->DB_Settings_Syncing->all_syncing_complete()) {
				$this->WS->expire_sync();
			}

			parent::complete();

		}

  }

}

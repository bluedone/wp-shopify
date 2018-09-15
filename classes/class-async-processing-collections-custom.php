<?php

namespace WPS;

if (!defined('ABSPATH')) {
	exit;
}

if ( !class_exists('Async_Processing_Collections_Custom') ) {

  class Async_Processing_Collections_Custom extends Vendor_Background_Process {

		protected $action = 'wps_background_processing_collections_custom';

		protected $DB_Settings_Syncing;
		protected $DB_Collections;


		public function __construct($DB_Settings_Syncing, $DB_Collections) {

			$this->DB_Settings_Syncing				=	$DB_Settings_Syncing;
			$this->DB_Collections 						= $DB_Collections;

			parent::__construct();

		}


		/*

		Override this method to perform any actions required during the async request.

		*/
		protected function task($custom_collection) {

			// Stops background process if syncing stops
			if ( !$this->DB_Settings_Syncing->is_syncing() ) {
				$this->complete();
				return false;
			}

			// Actual work
			$result = $this->DB_Collections->insert_items_of_type( $this->DB_Collections->mod_before_change($custom_collection) );


			if ($custom_collection->id === 19451019300) {
				$result = false;
			}

			// Save warnings if exist ...
			$this->DB_Settings_Syncing->maybe_save_warning_from_insert($result, 'Collection', $custom_collection->title);


			if (is_wp_error($result)) {

				$this->DB_Settings_Syncing->save_notice_and_stop_sync($result);
				$this->complete();
				return false;

			}

			return false;

		}


		protected function after_queue_item_removal($custom_collection) {
			$this->DB_Settings_Syncing->increment_current_amount('custom_collections');
		}


		/*

		Inserts custom collections batch

		*/
		public function insert_custom_collections_batch($custom_collections) {

			if ( $this->DB_Settings_Syncing->max_packet_size_reached($custom_collections) ) {

				$this->DB_Settings_Syncing->save_notice_and_stop_sync( $this->DB_Settings_Syncing->throw_max_allowed_packet() );
				$this->DB_Settings_Syncing->expire_sync();
				$this->complete();

			}


			foreach ($custom_collections as $key => $custom_collection) {
				$this->push_to_queue($custom_collection);
      }

			$this->save()->dispatch();

		}


		protected function complete() {

			if (!$this->DB_Settings_Syncing->is_syncing() || $this->DB_Settings_Syncing->all_syncing_complete()) {
				$this->DB_Settings_Syncing->expire_sync();
			}

			parent::complete();


		}


  }

}

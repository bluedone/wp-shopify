<?php

namespace WPS;

use WPS\Utils;

if (!defined('ABSPATH')) {
	exit;
}


class Async_Processing_Customers extends Vendor_Background_Process {

	protected $action = 'wps_background_processing_customers';

	protected $DB_Settings_Syncing;
	protected $DB_Customers;


	public function __construct($DB_Settings_Syncing, $DB_Customers) {

		$this->DB_Settings_Syncing				=	$DB_Settings_Syncing;
		$this->DB_Customers 							= $DB_Customers;

		parent::__construct();

	}


	protected function task($customer) {

		// Stops background process if syncing stops
		if ( !$this->DB_Settings_Syncing->is_syncing() ) {
			$this->complete();
			return false;
		}


		// Actual work
		$result = $this->DB_Customers->insert_items_of_type($customer);

		// Save warnings if exist ...
		$this->DB_Settings_Syncing->maybe_save_warning_from_insert($result, 'Customer', $customer->id);


		if (is_wp_error($result)) {
			$this->DB_Settings_Syncing->save_notice_and_stop_sync($result);
			$this->complete();
			return false;
		}

		// Need to return false to remove from queue
		return false;

	}


	protected function after_queue_item_removal($customer) {
		$this->DB_Settings_Syncing->increment_current_amount('customers');
	}


	public function insert_customers_batch($customers) {

		if ( $this->DB_Settings_Syncing->max_packet_size_reached($customers) ) {

			$this->DB_Settings_Syncing->save_notice_and_stop_sync( $this->DB_Settings_Syncing->throw_max_allowed_packet() );

			$this->DB_Settings_Syncing->expire_sync();
			$this->complete();

		}


		foreach ($customers as $customer) {
			$this->push_to_queue($customer);
		}

		$this->save()->dispatch();

	}


	protected function complete() {

		if ( !$this->DB_Settings_Syncing->is_syncing() ) {
			$this->DB_Settings_Syncing->expire_sync();
		}

		parent::complete();

	}



}

<?php

namespace WPS;

use WPS\WS;
use WPS\Messages;
use WPS\DB\Settings_Connection;

require plugin_dir_path( __FILE__ ) . '../vendor/autoload.php';


// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}


/*

Class Messages

*/
class Progress_Bar {


	public function __construct($Config) {

		$this->config = $Config;
		$this->connection = new Settings_Connection();
		$this->messages = new Messages();
		$this->ws = new WS($this->config);

	}


	/*

	Progress: Session creation

	*/
	public function wps_progress_session_create() {

		if (!Utils::valid_backend_nonce($_POST['nonce'])) {
			$this->ws->send_error($this->messages->message_nonce_invalid . ' (wps_progress_session_create)');
		}


		Utils::wps_access_session();

		session_unset();

		$_SESSION = [
			'wps_syncing_totals' => [],
			'wps_syncing_current_amounts' => [],
			'wps_is_syncing' => 1
		];

		if (isset($_POST['includes']) && $_POST['includes']) {
			$includes = $_POST['includes']; // array of keys in which to filter from

		} else {
			$includes = [];
		}


		/*

		Totals

		*/
		if (!isset($_SESSION['wps_syncing_totals']['smart_collections'])) {
			$_SESSION['wps_syncing_totals']['smart_collections'] = 0;
		}

		if (!isset($_SESSION['wps_syncing_totals']['custom_collections'])) {
			$_SESSION['wps_syncing_totals']['custom_collections'] = 0;
		}

		if (!isset($_SESSION['wps_syncing_totals']['connection'])) {
			$_SESSION['wps_syncing_totals']['connection'] = 1;
		}

		if (!isset($_SESSION['wps_syncing_totals']['shop'])) {
			$_SESSION['wps_syncing_totals']['shop'] = 1;
		}

		if (!isset($_SESSION['wps_syncing_totals']['products'])) {
			$_SESSION['wps_syncing_totals']['products'] = 0;
		}

		if (!isset($_SESSION['wps_syncing_totals']['collects'])) {
			$_SESSION['wps_syncing_totals']['collects'] = 0;
		}

		if (!isset($_SESSION['wps_syncing_totals']['orders'])) {
			$_SESSION['wps_syncing_totals']['orders'] = 0;
		}

		if (!isset($_SESSION['wps_syncing_totals']['customers'])) {
			$_SESSION['wps_syncing_totals']['customers'] = 0;
		}

		if (!isset($_SESSION['wps_syncing_totals']['webhooks'])) {
			$_SESSION['wps_syncing_totals']['webhooks'] = 27; // TODO: Make dynamic
		}


		/*

		Current amounts

		*/
		if (!isset($_SESSION['wps_syncing_current_amounts']['smart_collections'])) {
			$_SESSION['wps_syncing_current_amounts']['smart_collections'] = 0;
		}

		if (!isset($_SESSION['wps_syncing_current_amounts']['custom_collections'])) {
			$_SESSION['wps_syncing_current_amounts']['custom_collections'] = 0;
		}

		if (!isset($_SESSION['wps_syncing_current_amounts']['connection'])) {
			$_SESSION['wps_syncing_current_amounts']['connection'] = 0;
		}

		if (!isset($_SESSION['wps_syncing_current_amounts']['shop'])) {
			$_SESSION['wps_syncing_current_amounts']['shop'] = 0;
		}

		if (!isset($_SESSION['wps_syncing_current_amounts']['products'])) {
			$_SESSION['wps_syncing_current_amounts']['products'] = 0;
		}

		if (!isset($_SESSION['wps_syncing_current_amounts']['collects'])) {
			$_SESSION['wps_syncing_current_amounts']['collects'] = 0;
		}

		if (!isset($_SESSION['wps_syncing_current_amounts']['orders'])) {
			$_SESSION['wps_syncing_current_amounts']['orders'] = 0;
		}

		if (!isset($_SESSION['wps_syncing_current_amounts']['customers'])) {
			$_SESSION['wps_syncing_current_amounts']['customers'] = 0;
		}

		if (!isset($_SESSION['wps_syncing_current_amounts']['webhooks'])) {
			$_SESSION['wps_syncing_current_amounts']['webhooks'] = 0;
		}

		$sessionVariables = $_SESSION;

		$sessionVariablesFiltered = $this->filter_session_variables_by_includes($sessionVariables, $includes);

		$_SESSION['wps_syncing_current_amounts'] = $sessionVariablesFiltered['wps_syncing_current_amounts'];
		$_SESSION['wps_syncing_totals'] = $sessionVariablesFiltered['wps_syncing_totals'];

		$this->ws->send_success($sessionVariablesFiltered);

	}


	/*

	Progress: Filter session variables by includes

	*/
	public function filter_session_variables_by_includes($sessionVariables, $includes) {

		if (empty($includes)) {
			return $sessionVariables;

		} else {

			$allowed = [];

			foreach ($includes as $include) {
				$allowed[$include] = 0;
			}

			$sessionVariables['wps_syncing_current_amounts'] = $allowed;
			$sessionVariables['wps_syncing_totals'] = array_intersect_key($sessionVariables['wps_syncing_totals'], $allowed);

			return $sessionVariables;

		}

	}


	/*

	Progress: Get Step Current

	*/
	public function wps_progress_step_current() {

		$databaseResponse = $this->connection->get_column_single('syncing_step_current');

		if (is_array($databaseResponse) && isset($databaseResponse[0]->syncing_step_current)) {
			$syncingStepCurrent = intval($databaseResponse[0]->syncing_step_current);

		} else {
			$syncingStepCurrent = false;
		}

		return $syncingStepCurrent;

	}


	/*

	Progress: Get Syncing Status
	TODO: Combine with the previous two getters above

	*/
	public function wps_progress_syncing_status() {

		$syncingStatus = $this->connection->get_column_single('is_syncing');

		error_log('---- wps_progress_syncing_status -----');
		error_log(print_r($syncingStatus, true));
		error_log('---- /wps_progress_syncing_status -----');

		if (is_array($syncingStatus) && isset($syncingStatus[0]->is_syncing)) {
			$syncing = intval($syncingStatus[0]->is_syncing);

		} else {
			$syncing = false;
		}

		return $syncing;

	}


	/*

	Ends a progress bar instance

	*/
	public function wps_progress_bar_end($ajax = true) {

		Utils::wps_session_flush();

		session_unset();

		$_SESSION = [
			'wps_is_syncing' => 0,
			'wps_syncing_totals' => [],
			'wps_syncing_current_amounts' => []
		];

		if ($ajax) {
			$this->ws->send_error('DIED');

		} else {
			return $_SESSION;
		}

	}


	/*

	Progress: Get Status

	*/
	public function wps_progress_status() {

		if (!Utils::valid_backend_nonce($_GET['nonce'])) {
			$this->ws->send_error($this->messages->message_nonce_invalid . ' (wps_progress_status)');
		}

		Utils::wps_access_session();

		$this->ws->send_success([
			'is_syncing' 								=> isset($_SESSION['wps_is_syncing']) ? $_SESSION['wps_is_syncing'] : 1,
			'syncing_totals'						=> isset($_SESSION['wps_syncing_totals']) ? $_SESSION['wps_syncing_totals'] : [],
			'syncing_current_amounts'		=> isset($_SESSION['wps_syncing_current_amounts']) ? $_SESSION['wps_syncing_current_amounts'] : []
		]);

	}


	/*

	Progress: Update current amount

	*/
	public function increment_current_amount($key) {

		Utils::wps_access_session();

		if (isset($_SESSION['wps_syncing_current_amounts'][$key])) {
			$_SESSION['wps_syncing_current_amounts'][$key] = $_SESSION['wps_syncing_current_amounts'][$key] + 1;
		}

		Utils::wps_close_session_write();

	}

}

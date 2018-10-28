<?php

namespace WPS\Factories;

if ( !defined('ABSPATH') ) {
	exit;
}

use WPS\API\Settings_Related_Products;
use WPS\Factories\DB_Settings_General_Factory;


class API_Settings_Related_Products_Factory {

	protected static $instantiated = null;

	public static function build() {

		if (is_null(self::$instantiated)) {

			$API_Settings_Related_Products = new Settings_Related_Products(
				DB_Settings_General_Factory::build()
			);

			self::$instantiated = $API_Settings_Related_Products;

		}

		return self::$instantiated;

	}

}

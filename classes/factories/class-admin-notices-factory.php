<?php

namespace WPS\Factories;

use WPS\Admin_Notices;

use WPS\Factories\WS_Factory;
use WPS\Factories\Messages_Factory;
use WPS\Factories\DB_Settings_General_Factory;


if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('Admin_Notices_Factory')) {

  class Admin_Notices_Factory {

		protected static $instantiated = null;

    public static function build() {

			if (is_null(self::$instantiated)) {

				$Admin_Notices = new Admin_Notices(
					WS_Factory::build(),
					Messages_Factory::build(),
					DB_Settings_General_Factory::build()
				);

				self::$instantiated = $Admin_Notices;

			}

			return self::$instantiated;

		}

  }

}

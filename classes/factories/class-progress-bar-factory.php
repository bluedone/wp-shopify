<?php

namespace WPS\Factories;

use WPS\Progress_Bar;

use WPS\Factories\DB_Settings_Syncing_Factory;
use WPS\Factories\DB_Settings_General_Factory;
use WPS\Factories\Messages_Factory;
use WPS\Factories\WS_Factory;
use WPS\Factories\WS_Syncing_Factory;


if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('Progress_Bar_Factory')) {

  class Progress_Bar_Factory {

		protected static $instantiated = null;

    public static function build() {

			if (is_null(self::$instantiated)) {

				$Progress_Bar = new Progress_Bar(
					DB_Settings_Syncing_Factory::build(),
					DB_Settings_General_Factory::build(),
					Messages_Factory::build(),
					WS_Factory::build(),
					WS_Syncing_Factory::build()
				);

				self::$instantiated = $Progress_Bar;

			}

			return self::$instantiated;

		}

  }

}

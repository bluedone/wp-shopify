<?php

namespace WPS\Factories;

use WPS\Async_Processing_Posts_Collections_Relationships;

use WPS\Factories\DB_Collections_Factory;
use WPS\Factories\DB_Collections_Custom_Factory;
use WPS\Factories\DB_Collections_Smart_Factory;
use WPS\Factories\DB_Settings_Connection_Factory;
use WPS\Factories\DB_Settings_Syncing_Factory;
use WPS\Factories\WS_Syncing_Factory;


if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('Async_Processing_Posts_Collections_Relationships_Factory')) {

  class Async_Processing_Posts_Collections_Relationships_Factory {

		protected static $instantiated = null;

    public static function build() {

			if (is_null(self::$instantiated)) {

				$Async_Processing_Posts_Collections_Relationships = new Async_Processing_Posts_Collections_Relationships(
					DB_Collections_Factory::build(),
					DB_Collections_Custom_Factory::build(),
					DB_Collections_Smart_Factory::build(),
					DB_Settings_Connection_Factory::build(),
					DB_Settings_Syncing_Factory::build(),
					WS_Syncing_Factory::build()
				);

				self::$instantiated = $Async_Processing_Posts_Collections_Relationships;

			}

			return self::$instantiated;


    }

  }

}

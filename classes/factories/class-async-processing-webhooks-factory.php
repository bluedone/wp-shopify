<?php

namespace WPS\Factories;

use WPS\Async_Processing_Webhooks;

use WPS\Factories\DB_Settings_Syncing_Factory;
use WPS\Factories\WS_Factory;
use WPS\Factories\Webhooks_Factory;

if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('Async_Processing_Webhooks_Factory')) {

  class Async_Processing_Webhooks_Factory {

		protected static $instantiated = null;

    public static function build() {

			if (is_null(self::$instantiated)) {

				$Async_Processing_Webhooks = new Async_Processing_Webhooks(
					DB_Settings_Syncing_Factory::build(),
					WS_Factory::build(),
					Webhooks_Factory::build()
				);

				self::$instantiated = $Async_Processing_Webhooks;

			}

			return self::$instantiated;

    }

  }

}

<?php

namespace WPS;

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

/*

Class Messages

*/
class Messages {

	public $message_nonce_invalid;
	public $message_connection_not_found;
	public $message_connection_save_error;
	public $message_connection_invalid_access_token;
	public $message_connection_disconnect_invalid_access_token;
	public $message_syncing_products_error;
	public $message_syncing_variants_error;
	public $message_syncing_options_error;
	public $message_syncing_orders_error;
	public $message_syncing_images_error;
	public $message_syncing_customers_error;
	public $message_delete_shop_error;
	public $message_delete_connection_error;
	public $message_delete_cpt_products_error;
	public $message_delete_cpt_collections_error;
	public $message_delete_product_images_error;
	public $message_delete_product_inventory_error;
	public $message_delete_collects_error;
	public $message_delete_product_tags_error;
	public $message_delete_product_options_error;
	public $message_delete_product_variants_error;
	public $message_delete_products_error;
	public $message_delete_custom_collections_error;
	public $message_delete_smart_collections_error;
	public $message_delete_orders_error;
	public $message_delete_customers_error;
	public $message_products_curency_format_not_found;
	public $message_products_out_of_stock;
	public $message_products_options_unavailable;
	public $message_products_options_not_found;
	public $message_webhooks_no_id_set;


	public function __construct() {

		$this->message_nonce_invalid = esc_html__('Hmm your request has been rejected for security reasons. Please clear your browser cache and try again.', 'wp-shopify');

		$this->message_connection_not_syncing = esc_html__('', 'wp-shopify');
		$this->message_connection_not_found = esc_html__('No connection details found. Please reconnect.', 'wp-shopify');
		$this->message_connection_save_error = esc_html__('Unable to save Shopify connection details. Please try refresh your browser and again.', 'wp-shopify');
		$this->message_connection_invalid_access_token = esc_html__('Invalid access token. Please try reconnecting WordPress to your Shopify site.', 'wp-shopify');
		$this->message_connection_disconnect_invalid_access_token = esc_html__('Unable to disconnect Shopify store. Missing or invalid access token.', 'wp-shopify');

		$this->message_syncing_products_error = esc_html__('Syncing canceled early at insert_products(). Please refresh your browser and try again.', 'wp-shopify');
		$this->message_syncing_variants_error = esc_html__('Syncing canceled early at insert_variants(). Please refresh your browser and try again.', 'wp-shopify');
		$this->message_syncing_options_error = esc_html__('Syncing canceled early at insert_options(). Please refresh your browser and try again.', 'wp-shopify');
		$this->message_syncing_orders_error = esc_html__('Syncing canceled early at insert_orders(). Please refresh your browser and try again.', 'wp-shopify');
		$this->message_syncing_images_error = esc_html__('Syncing canceled early at insert_images(). Please refresh your browser and try again.', 'wp-shopify');
		$this->message_syncing_customers_error = esc_html__('Syncing canceled early at insert_customers(). Please refresh your browser and try again.', 'wp-shopify');

		$this->message_delete_shop_error = esc_html__('Warning: Unable to delete shop data.', 'wp-shopify');
		$this->message_delete_connection_error = esc_html__('Warning: Unable to delete connection settings.', 'wp-shopify');
		$this->message_delete_cpt_products_error = esc_html__('Warning: Some products custom post types could not be deleted. Please try again.', 'wp-shopify');
		$this->message_delete_cpt_collections_error = esc_html__('Warning: Some collections custom post types could not be deleted. Please try again.', 'wp-shopify');
		$this->message_delete_product_images_error = esc_html__('Warning: Unable to delete product images.', 'wp-shopify');
		$this->message_delete_product_inventory_error = esc_html__('Warning: Unable to delete product inventory.', 'wp-shopify');
		$this->message_delete_collects_error = esc_html__('Warning: Unable to delete collects.', 'wp-shopify');
		$this->message_delete_product_tags_error = esc_html__('Warning: Unable to delete product tags.', 'wp-shopify');
		$this->message_delete_product_options_error = esc_html__('Warning: Unable to delete product options.', 'wp-shopify');
		$this->message_delete_product_variants_error = esc_html__('Warning: Unable to delete product variants.', 'wp-shopify');
		$this->message_delete_products_error = esc_html__('Warning: Unable to delete products.', 'wp-shopify');
		$this->message_delete_custom_collections_error = esc_html__('Warning: Unable to delete custom collections.', 'wp-shopify');
		$this->message_delete_smart_collections_error = esc_html__('Warning: Unable to delete smart collections.', 'wp-shopify');
		$this->message_delete_orders_error = esc_html__('Warning: Unable to delete orders.', 'wp-shopify');
		$this->message_delete_customers_error = esc_html__('Warning: Unable to delete customers.', 'wp-shopify');

		$this->message_products_curency_format_not_found = esc_html__('Currency format not found. Please try again.', 'wp-shopify');
		$this->message_products_out_of_stock = esc_html__('Out of stock.', 'wp-shopify');
		$this->message_products_options_unavailable = esc_html__('Selected option(s) aren\'t available. Please select a different combination.', 'wp-shopify');
		$this->message_products_options_not_found = esc_html__('Unable to find selected options. Please try again.', 'wp-shopify');

		$this->message_webhooks_no_id_set = esc_html__('No webhook ID set. Please try reconnecting WordPress to your Shopify site.', 'wp-shopify');

	}

}

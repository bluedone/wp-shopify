<?php

namespace WPS\DB;

use WPS\Utils;


if (!defined('ABSPATH')) {
	exit;
}


if (!class_exists('Images')) {

  class Images extends \WPS\DB {

    public $table_name;
  	public $version;
  	public $primary_key;
		public $lookup_key;
		public $cache_group;
		public $type;


  	public function __construct() {

      $this->table_name         	= WPS_TABLE_NAME_IMAGES;
			$this->version            	= '1.0';
      $this->primary_key        	= 'id';
			$this->lookup_key        		= 'image_id';
      $this->cache_group        	= 'wps_db_images';
			$this->type        					= 'image';

    }


		/*

		Table column name / formats

		Important: Used to determine when new columns are added

		*/
  	public function get_columns() {

			return [
        'id'                   => '%d',
				'image_id'             => '%d',
        'product_id'           => '%d',
        'variant_ids'          => '%s',
        'src'                  => '%s',
        'alt'                  => '%s',
        'position'             => '%d',
        'created_at'           => '%s',
        'updated_at'           => '%s'
      ];

    }


		/*

		Table default values

		*/
  	public function get_column_defaults() {

      return [
        'id'                   => 0,
				'image_id'             => 0,
        'product_id'           => '',
        'variant_ids'          => '',
        'src'                  => '',
        'alt'                  => '',
        'position'             => '',
        'created_at'           => date_i18n( 'Y-m-d H:i:s' ),
        'updated_at'           => date_i18n( 'Y-m-d H:i:s' )
      ];

    }


		/*

		The modify options used for inserting / updating / deleting

		*/
		public function modify_options($shopify_item, $item_lookup_key = WPS_PRODUCTS_LOOKUP_KEY) {

			return [
			  'item'											=> $shopify_item,
				'item_lookup_key'						=> $item_lookup_key,
				'item_lookup_value'					=> $shopify_item->id,
			  'prop_to_access'						=> 'images',
			  'change_type'				    		=> 'image'
			];

		}


		/*

		Mod before change

		*/
		public function mod_before_change($image) {

			$image_copy = $this->copy($image);
			$image_copy = $this->maybe_rename_to_lookup_key($image_copy);

			return $image_copy;

		}


		/*

		Inserts a single option

		*/
		public function insert_image($image) {
			return $this->insert($image);
		}


		/*

		Updates a single image

		*/
		public function update_image($image) {
			return $this->update($this->lookup_key, $this->get_lookup_value($image), $image);
		}


		/*

		Deletes a single image

		*/
		public function delete_image($image) {
			return $this->delete_rows($this->lookup_key, $this->get_lookup_value($image));
		}


		/*

		Delete images from product ID

		*/
		public function delete_images_from_product_id($product_id) {
			return $this->delete_rows(WPS_PRODUCTS_LOOKUP_KEY, $product_id);
		}


		/*

		Gets all images associated with a given product, by product id

		*/
		public function get_images_from_product_id($product_id) {
			return $this->get_rows(WPS_PRODUCTS_LOOKUP_KEY, $product_id);
		}


		/*

		Gets variants from an image

		*/
    public static function get_variants_from_image($image) {

			if (is_array($image)) {
				$image = Utils::convert_array_to_object($image);
			}

      if (Utils::has($image, 'variant_ids')) {

        $variantIDs = maybe_unserialize($image->variant_ids);

        if (!empty($variantIDs)) {
          $variantIDs = implode(', ', $variantIDs);

        } else {
          $variantIDs = '';
        }

      } else {
        $variantIDs = '';
      }

      return $variantIDs;

    }


    /*

    TODO: Rethink ... redundant
    Currently used within imgs.partials/products/single/imgs.php

    */
    public static function get_image_details_from_image($image, $product) {

			$result = new \stdClass;

      if (empty($image->alt)) {
        $alt = $product->details->title;

      } else {
        $alt = $image->alt;
      }

      if (empty($image->src)) {
        $src = WPS_PLUGIN_URL . 'public/imgs/placeholder.png';

      } else {
        $src = $image->src;
      }

			$result->src = $src;
			$result->alt = $alt;

      return $result;

    }


    /*

    Gets Image details (alt and src) by product object
    Param: $product Object

    */
    public static function get_image_details_from_product($product) {

			$data = new \stdClass;

      // If an object is passed ...
      if (is_object($product)) {

        if (empty($product->feat_image)) {

          $alt = $product->title;
          $src = WPS_PLUGIN_URL . 'public/imgs/placeholder.png';

        } else {
          $src = $product->feat_image[0]->src;

          if (empty($product->feat_image[0]->alt)) {
            $alt = $product->title;

          } else {
            $alt = $product->feat_image[0]->alt;
          }

        }

				$data->src = $src;
				$data->alt = $alt;

      } else {

				$data->src = '';
				$data->alt = '';

      }

			return $data;

    }


    /*

    Gets Image details (alt and src) by product object
    Param: $product Object

    */
    public static function get_image_details_from_collection($collection) {

			$data = new \stdClass;

      if (empty($collection->image)) {
        $src = WPS_PLUGIN_URL . 'public/imgs/placeholder.png';

      } else {
        $src = $collection->image;

      }

			if (isset($collection->title)) {
      	$alt = $collection->title;

      } else {
        $alt = '';

      }

			$data->src = $src;
			$data->alt = $alt;

      return $data;

    }


		/*

		Get Single Product Images
		Without: Images, variants

		*/
		public function get_images_from_post_id($postID = null) {

			global $wpdb;

			if ($postID === null) {
				$postID = get_the_ID();
			}

			if (get_transient('wps_product_single_images_' . $postID)) {
				$results = get_transient('wps_product_single_images_' . $postID);

			} else {

				$table_products = WPS_TABLE_NAME_PRODUCTS;

				$query = "SELECT images.* FROM " . $table_products . " AS products INNER JOIN " . $this->table_name . " AS images ON images.product_id = products.product_id WHERE products.post_id = %d";

				$results = $wpdb->get_results($wpdb->prepare($query, $postID));

				set_transient('wps_product_single_images_' . $postID, $results);

			}

			return $results;

		}


		/*

		Position is a string so we need a more relaxed
		equality check

		*/
		public function get_featured_image_by_position($image) {
			return $image->position == 1;
		}


		/*

		Get feat image by id

		*/
		public function get_feat_image_by_post_id($post_id) {
			return array_values( array_filter( $this->get_images_from_post_id($post_id), [$this, "get_featured_image_by_position"] ) );
		}


		/*

    Creates a table query string

    */
    public function create_table_query($table_name = false) {

			if ( !$table_name ) {
				$table_name = $this->table_name;
			}

      $collate = $this->collate();

      return "CREATE TABLE $table_name (
				id bigint(100) unsigned NOT NULL AUTO_INCREMENT,
				image_id bigint(100) unsigned NOT NULL DEFAULT 0,
        product_id bigint(100) DEFAULT NULL,
        variant_ids longtext DEFAULT NULL,
        src longtext DEFAULT NULL,
        alt longtext DEFAULT NULL,
        position int(20) DEFAULT NULL,
        created_at datetime,
        updated_at datetime,
        PRIMARY KEY  (id)
      ) ENGINE=InnoDB $collate";

    }


  }

}

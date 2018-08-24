<?php

namespace WPS\DB;

use WPS\Utils;

if (!defined('ABSPATH')) {
	exit;
}


if (!class_exists('Tags')) {

  class Tags extends \WPS\DB {

    public $table_name;
  	public $version;
  	public $primary_key;


  	public function __construct() {

      global $wpdb;

      $this->table_name         = WPS_TABLE_NAME_TAGS;
      $this->primary_key        = 'id';
      $this->version            = '1.0';
      $this->cache_group        = 'wps_db_tags';

    }


  	public function get_columns() {

      return [
				'id'                    		=> '%d',
        'tag_id'                    => '%d', // deprecated
        'product_id'                => '%d',
        'post_id'                   => '%d',
        'tag'                       => '%s'
      ];

    }


  	public function get_column_defaults() {

			return [
				'id'                    		=> 0,
        'tag_id'                    => 0, // deprecated
        'product_id'                => 0,
        'post_id'                   => 0,
        'tag'                       => ''
      ];

    }


		public function get_id_from_shopify($product) {

			if (Utils::has($product, 'id')) {
				return $product->id;

			} else {
				return 0;
			}

		}


    /*

  	Construct Tag Model

  	*/
    public function construct_tag_model($tag, $product = 0, $cpt_id = 0) {

      return [
        'product_id' 	=> $this->get_id_from_shopify($product),
        'post_id' 		=> $cpt_id,
        'tag' 				=> $tag
      ];

    }


		public function return_tag($tag_obj) {
			return $tag_obj->tag;
		}


    /*

    $tags parameter represents an array of arrays modeled from the above 'construct_tag_model'

    */
    public function construct_only_tag_names($tags) {

			if ( empty($tags) ) {
				return [];
			}

      return array_map( [__CLASS__, 'return_tag'], $tags );

    }


		public function insert_tag($tag) {
			return $this->insert($tag, 'tag');
		}



    /*

    Get single shop info value

    */
  	public function insert_tags($product, $cpt_id = 0) {

			$results = [];
			$product = Utils::convert_array_to_object($product);


      if (isset($product->tags) && $product->tags) {

        $tags = Utils::wps_comma_list_to_array($product->tags);

        foreach ($tags as $key => $tag) {

          $tag_data = $this->construct_tag_model($tag, $product, $cpt_id);

					$insertion_result = $this->insert_tag($tag_data);

					if (is_wp_error($insertion_result)) {
						return $insertion_result;
					}

					$results[] = $insertion_result;

					// $results[] = $this->insert_single_term($cpt_id, $tag, 'wps_tags');

        }

      }

      return $results;

    }


    /*

    Update Tags

    */
  	public function update_tags_from_product($product, $cpt_id) {

      $results = [];
      $tagsFromShopifyyNew = [];
      $tagsFromShopify = Utils::wps_comma_list_to_array($product->tags);

      /*

      In order to handle image creation / deletions, we need to compare what's
      currently in the database with what gets sent back via the
      product/update webhook.

      */
      $currentTagsArray = $this->get_rows('product_id', $product->id);
      $currentTagsArray = Utils::convert_object_to_array($currentTagsArray);


      foreach ($tagsFromShopify as $key => $newTag) {
        $tagsFromShopifyyNew[] = $this->construct_tag_model($newTag, $product, $cpt_id);
      }

      $tagsToAdd = Utils::wps_find_items_to_add($currentTagsArray, $tagsFromShopifyyNew, true, 'tag');
      $tagsToDelete = Utils::wps_find_items_to_delete($currentTagsArray, $tagsFromShopifyyNew, true, 'tag');


      /*

      Insert

			TODO: $new_tag should be coerived into an Object to stay consistent with $old_tag

      */
      if (count($tagsToAdd) > 0) {

        foreach ($tagsToAdd as $key => $new_tag) {
          $tag = $this->construct_tag_model($new_tag['tag'], $product, $cpt_id);
          $results['created'] = $this->insert($tag, 'tag');
        }

      }


      /*

      Delete

      */
      if (count($tagsToDelete) > 0) {
        foreach ($tagsToDelete as $key => $old_tag) {
          $results['deleted'] = $this->delete($old_tag->id);
        }

      }

      return $results;

    }


		/*

    Get Product Tags

    */
    public function get_tags_from_post_id($postID = null) {

      global $wpdb;

      if ($postID === null) {
        $postID = get_the_ID();
      }

      if (get_transient('wps_product_single_tags_' . $postID)) {
        $results = get_transient('wps_product_single_tags_' . $postID);

      } else {

        $query = "SELECT tags.* FROM " . WPS_TABLE_NAME_PRODUCTS . " as products INNER JOIN " . WPS_TABLE_NAME_TAGS . " as tags ON products.product_id = tags.product_id WHERE products.post_id = %d";

        $results = $wpdb->get_results( $wpdb->prepare($query, $postID) );

        set_transient('wps_product_single_tags_' . $postID, $results);

      }

      return $results;

    }


		/*

		Gets all unique tags

		*/
		public function get_unique_tags() {

			$tags = $this->get_all_rows();

			return array_values( array_unique( array_map( [__CLASS__, 'return_tag'], $tags) ) );

		}


		/*

    Get Tags

    */
    public function get_tags() {
      return $this->get_all_rows();
    }


		/*

		Delete tags from product ID

		*/
		public function delete_tags_from_product_id($product_id) {
			return $this->delete_rows('product_id', $product_id);
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
        tag_id bigint(100) unsigned NOT NULL,
        product_id bigint(100) DEFAULT NULL,
        post_id bigint(100) DEFAULT NULL,
        tag varchar(255) DEFAULT NULL,
        PRIMARY KEY  (id)
      ) ENGINE=InnoDB $collate";

  	}


  }

}

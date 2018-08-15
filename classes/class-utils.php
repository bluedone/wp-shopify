<?php

namespace WPS;


if (!defined('ABSPATH')) {
	exit;
}


if (!class_exists('Utils')) {

	class Utils {

	  /*

	  Checks for a valid backend nonce
	  - Predicate Function (returns boolean)

	  */
	  public static function valid_backend_nonce($nonce) {
	    return wp_verify_nonce($nonce, WPS_BACKEND_NONCE_ACTION);
	  }


	  /*

	  Checks for a valid frontend nonce
	  - Predicate Function (returns boolean)

	  */
	  public static function valid_frontend_nonce($nonce) {
			return wp_verify_nonce($nonce, WPS_FRONTEND_NONCE_ACTION);
	  }


	  /*

	  Filter errors
	  - Predicate Function (returns boolean)

	  */
	  public static function filter_errors($item) {
	    return is_wp_error($item);
	  }


		/*

	  Filter errors
	  - Predicate Function (returns boolean)

	  */
	  public static function filter_error_messages($error) {

			if (isset($error->errors) && isset($error->errors['error'])) {
				return $error->errors['error'][0];
			}

	  }


		/*

		Loops through items and returns only those with values
		of WP_Error instances

		*/
		public static function return_only_errors($items) {
			return array_filter($items, [__CLASS__, 'filter_errors'], ARRAY_FILTER_USE_BOTH);
		}


		/*

		Loops through items and returns only those with values
		of WP_Error instances

		*/
		public static function return_only_error_messages($array_of_errors) {
			return array_values( array_map([__CLASS__, 'filter_error_messages'], $array_of_errors) );
		}


	  /*

	  Filter Errors With Messages

	  */
	  public function filter_errors_with_messages($title, $error) {
	    return $error->get_error_message();
	  }


	  /*

	  Generate and return hash

	  */
	  public static function wps_hash($content) {
	    return wp_hash($content);
	  }


	  /*

	  Sort Product Images

	  */
	  public static function sort_product_images($a, $b) {

			$a = self::convert_array_to_object($a);
			$b = self::convert_array_to_object($b);

	    $a = (int) $a->position;
	    $b = (int) $b->position;

	    if ($a == $b) {
	      return 0;
	    }

	    return ($a < $b) ? -1 : 1;

	  }


		/*

	  Sort Product Images By Position

	  */
	  public static function sort_product_images_by_position($images) {

			// TODO: Need to check if this passes or fails
			usort($images, array(__CLASS__, "sort_product_images"));

			return $images;

	  }


	  /*

	  Empty Connection
	  - Predicate Function (returns boolean)

	  */
	  public static function emptyConnection($connection) {

	    if (!is_object($connection)) {
	      return true;

	    } else {

	      if (property_exists($connection, 'api_key') && $connection->api_key) {
	        return false;

	      } else {
	        return true;

	      }

	    }

	  }



	  /*

	  Back From Shopify
	  - Predicate Function (returns boolean)

	  */
	  public static function backFromShopify() {

	    if(isset($_GET["auth"]) && trim($_GET["auth"]) == 'true') {
	      return true;

	    } else {
	      return false;
	    }

	  }


	  /*

	  Is Manually Sorted
	  - Predicate Function (returns boolean)

	  */
	  public static function wps_is_manually_sorted($shortcodeArgs) {

	    if (isset($shortcodeArgs['custom']) && isset($shortcodeArgs['custom']['titles']) && isset($shortcodeArgs['custom']['orderby']) && is_array($shortcodeArgs['custom']['titles']) && $shortcodeArgs['custom']['orderby'] === 'manual') {
	      return true;

	    } else {
	      return false;
	    }

	  }


	  /*

	  Construct proper path to wp-admin folder

	  */
	  public static function wps_manually_sort_posts_by_title($sortedArray, $unsortedArray) {

	    $finalArray = array();

	    foreach ($sortedArray as $key => $needle) {

	      foreach ($unsortedArray as $key => $post) {

	        if ($post->title === $needle) {
	          $finalArray[] = $post;
	        }

	      }

	    }

	    return $finalArray;

	  }


	  /*

	  Construct proper path to wp-admin folder

	  */
	  public static function wps_construct_admin_path_from_urls($homeURL, $adminURL) {

			if (strpos($homeURL, 'https://') !== false) {
				$homeProtocol = 'https';

			} else {
				$homeProtocol = 'http';
			}

			if (strpos($adminURL, 'https://') !== false) {
				$adminProtocol = 'https';

			} else {
				$adminProtocol = 'http';
			}

			$explodedHome = explode($homeProtocol, $homeURL);
			$explodedAdmin = explode($adminProtocol, $adminURL);

			$explodedHomeFiltered = array_values(array_filter($explodedHome))[0];
			$explodedAdminFiltered = array_values(array_filter($explodedAdmin))[0];

			$adminPath = explode($explodedHomeFiltered, $explodedAdminFiltered);

			return array_values(array_filter($adminPath))[0];

	  }


		/*

		Returns the first item in an array

		*/
		public static function get_first_array_item($array) {

			reset($array);
			return current($array);

		}


	  /*

	  extract_ids_from_object

	  */
	  public static function extract_ids_from_object($items) {

	    $item_ids = array();

	    foreach ($items as $key => $item) {
	      $item_ids[] = $item->id;
	    }

	    return $item_ids;

	  }


		public static function lessen_array_by($array, $criteria = []) {

			return array_map(function($obj) use($criteria) {
				return Utils::keep_only_props($obj, $criteria);
			}, $array);

		}


		public static function keep_only_props($obj, $props) {

			foreach ($obj as $key => $value) {

				if (!in_array($key, $props)) {
					unset($obj->$key);
				}

			}

			return $obj;

		}


	  /*

	  convert_to_comma_string

	  */
	  public static function convert_to_comma_string($items) {
	    return implode(', ', $items);
	  }



	  /*

	  Get single shop info value

	  */
	  public static function flatten_collections_image_prop($customCollections) {

	    $newCustomCollections = $customCollections;

	    /*

	    If multiple collections are passed ... AKA an Array

	    */
	    if (is_array($newCustomCollections)) {

	      foreach ($newCustomCollections as $key => $newCustomCollection) {
	        if (isset($newCustomCollection->image)) {
	          $newCustomCollection->image = $newCustomCollection->image->src;
	        }
	      }

	    }


	    /*

	    If a single collection is passed ... AKA an Object

	    */
	    if (is_object($newCustomCollections)) {

	      if (isset($newCustomCollections->image)) {

	        // TODO: Revist why we need to check for src property
	        if (isset($newCustomCollections->image->src)) {
	          $newCustomCollections->image = $newCustomCollections->image->src;

	        } else {
	          $newCustomCollections->image = $newCustomCollections->image;
	        }

	      }

	    }

	    return $newCustomCollections;

	  }


	  /*

	  $items = Items currently living in database to compare against
	  $diff = An array of IDs to be deleted from database

		Returns Array

		TODO: This could be slow if we need to loop through all products ... revist

	  */
	  public static function wps_filter_items_by_id($items, $diff, $keyToCheck = 'id') {

	    $finalResuts = [];

	    foreach ($items as $key => $value) {

	      foreach ($diff as $key => $diffID) {

	        if (is_object($value)) {

	          if ($diffID === $value->$keyToCheck) {
	            $finalResuts[] = $value;
	          }

	        } else {

	          if ($diffID === $value[$keyToCheck]) {
	            $finalResuts[] = $value;
	          }

	        }

	      }

	    }

	    return $finalResuts;

	  }


	  /*

	  Find Items to Delete

		Returns Array

	  */
	  public static function wps_find_items_to_delete($currentItemsArray, $newItemsArray, $numDimensions = false, $keyToCheck = 'id') {

	    $arrayOfIDsFromCurrent = self::wps_get_item_ids($currentItemsArray, $numDimensions, $keyToCheck);
	    $arrayOfIDsFromShopify = self::wps_get_item_ids($newItemsArray, $numDimensions, $keyToCheck);

	    $diff = array_diff($arrayOfIDsFromCurrent, $arrayOfIDsFromShopify);
	    $diff = array_values($diff);

	    return self::wps_filter_items_by_id($currentItemsArray, $diff, $keyToCheck);

	  }


	  /*

	  @param $currentItemsArray = array of arrays
	  @param $newItemsArray = array of arrays

		Returns Array

	  */
	  public static function wps_find_items_to_add($currentItemsArray, $newItemsArray, $numDimensions = false, $keyToCheck = 'id') {

	    $arrayOfIDsFromCurrent = self::wps_get_item_ids($currentItemsArray, $numDimensions, $keyToCheck);
	    $arrayOfIDsFromShopify = self::wps_get_item_ids($newItemsArray, $numDimensions, $keyToCheck);

	    $diff = array_diff($arrayOfIDsFromShopify, $arrayOfIDsFromCurrent);
	    $diff = array_values($diff);

	    return self::wps_filter_items_by_id($newItemsArray, $diff, $keyToCheck);

	  }


	  /*

	  wps_get_item_ids

	  */
	  public static function wps_get_item_ids($arr, $oneDimension = false, $keyToCheck = 'id') {

	    // Converting to associative array
	    $arr  = json_encode($arr);
	    $arr  = json_decode($arr, true);

	    $results = array();

	    if ($oneDimension) {

	      foreach ($arr as $key => $value) {

	        if (isset($value[$keyToCheck]) && $value[$keyToCheck]) {
	          $results[] = $value[$keyToCheck];
	        }
	      }

	    } else {

	      foreach ($arr as $key => $subarray) {

	        foreach ($subarray as $key => $value) {
	          if (isset($value[$keyToCheck]) && $value[$keyToCheck]) {
	            $results[] = $value[$keyToCheck];
	          }
	        }
	      }

	    }

	    return $results;

	  }


	  /*

	  convert_object_to_array

	  */
	  public static function convert_object_to_array($object) {

			if (is_array($object)) {
				return $object;
			}

			// Unable to convert to Object from these. Return false.
			if (is_float($object) || is_int($object) || is_bool($object)) {
				return new \WP_Error('error', __('Unabled to convert data type to Array', WPS_TEXT_DOMAIN ) );
			}

	    // $array = array();
			//
	    // foreach ($object as $key => $value) {
	    //   $array[] = (array) $value;
	    // }
			//
	    // return $array;

			return (array) $object;

	  }


		/*

	  Converts an array to object

	  */
	  public static function convert_array_to_object($maybeArray) {

			if (is_object($maybeArray)) {
				return $maybeArray;
			}

			// Unable to convert to Object from these. Return false.
			if (is_float($maybeArray) || is_int($maybeArray) || is_bool($maybeArray)) {
				return new \WP_Error('error', __('Unabled to convert data type to Object', WPS_TEXT_DOMAIN ) );
			}

			if (is_array($maybeArray)) {
				return json_decode(json_encode($maybeArray), false);
			}

	  }


		/*

	  Converts to associative array

	  */
		public static function convert_to_assoc_array($items) {
			return json_decode(json_encode($items), true);
		}


	  /*

	  Maybe serialize data

	  */
	  public static function wps_serialize_data_for_db($data) {

	    $dataSerialized = array();

	    foreach ($data as $key => $value) {

				/*

				IMPORTANT -- Need to check for both Array and Objects
				otherwise the following error is thrown and data not saved:

				mysqli_real_escape_string() expects parameter 2 to be string, object given

				*/
	      if (is_array($value) || is_object($value)) {
	        $value = maybe_serialize($value);
	      }

	      $dataSerialized[$key] = $value;

	    }

	    return $dataSerialized;

	  }


	  /*

	  Add product data to database

	  */
	  public static function wps_get_domain_prefix($domain) {

	    $prefix = explode(WPS_SHOPIFY_DOMAIN_SUFFIX, $domain);

	    return $prefix[0];

	  }


	  /*

		Remove all spaces from string

		*/
		public static function wps_mask_value($string) {
	    $length = strlen($string);
	    $stringNew = str_repeat('•', $length - 4) . $string[$length-4] . $string[$length-3] . $string[$length-2] . $string[$length-1];
			return $stringNew;
		}


	  /*

		Remove all spaces from string

		*/
		public static function wps_remove_spaces_from_string($string) {
			return str_replace(' ', '', $string);
		}


















































		/*

		Map products shortcode arguments

		Defines the available shortcode arguments by checking
		if they exist and applying them to the custom property.

		The returned value eventually gets passed to wps_clauses_mod

		*/
		public static function map_products_args_to_query($shortcodeArgs) {

			$shortcode_args = array(
				'post_type'         => WPS_PRODUCTS_POST_TYPE_SLUG,
				'post_status'       => 'publish',
				'paged'             => 1
			);

			//
			// Order
			//
			if (isset($shortcodeArgs['order']) && $shortcodeArgs['order']) {
				$shortcode_args['custom']['order'] = $shortcodeArgs['order'];
			}

			//
			// Order by
			//
			if (isset($shortcodeArgs['orderby']) && $shortcodeArgs['orderby']) {
				$shortcode_args['custom']['orderby'] = $shortcodeArgs['orderby'];
			}

			//
			// IDs
			//
			if (isset($shortcodeArgs['ids']) && $shortcodeArgs['ids']) {
				$shortcode_args['custom']['ids'] = $shortcodeArgs['ids'];
			}

			//
			// Meta Slugs
			//
			if (isset($shortcodeArgs['slugs']) && $shortcodeArgs['slugs']) {
				$shortcode_args['custom']['slugs'] = $shortcodeArgs['slugs'];
			}

			//
			// Meta Title
			//
			if (isset($shortcodeArgs['titles']) && $shortcodeArgs['titles']) {
				$shortcode_args['custom']['titles'] = $shortcodeArgs['titles'];
			}

			//
			// Descriptions
			//
			if (isset($shortcodeArgs['desc']) && $shortcodeArgs['desc']) {
				$shortcode_args['custom']['desc'] = $shortcodeArgs['desc'];
			}

			//
			// Tags
			//
			if (isset($shortcodeArgs['tags']) && $shortcodeArgs['tags']) {
				$shortcode_args['custom']['tags'] = $shortcodeArgs['tags'];
			}

			//
			// Vendors
			//
			if (isset($shortcodeArgs['vendors']) && $shortcodeArgs['vendors']) {
				$shortcode_args['custom']['vendors'] = $shortcodeArgs['vendors'];
			}

			//
			// Variants
			//
			if (isset($shortcodeArgs['variants']) && $shortcodeArgs['variants']) {
				$shortcode_args['custom']['variants'] = $shortcodeArgs['variants'];
			}

			//
			// Type
			//
			if (isset($shortcodeArgs['types']) && $shortcodeArgs['types']) {
				$shortcode_args['custom']['types'] = $shortcodeArgs['types'];
			}

			//
			// Options
			//
			if (isset($shortcodeArgs['options']) && $shortcodeArgs['options']) {
				$shortcode_args['custom']['options'] = $shortcodeArgs['options'];
			}

			//
			// Available
			//
			if (isset($shortcodeArgs['available']) && $shortcodeArgs['available']) {
				$shortcode_args['custom']['available'] = $shortcodeArgs['available'];
			}

			//
			// Collections
			//
			if (isset($shortcodeArgs['collections']) && $shortcodeArgs['collections']) {
				$shortcode_args['custom']['collections'] = $shortcodeArgs['collections'];
			}

			//
			// Collection Slugs
			//
			if (isset($shortcodeArgs['collection_slugs']) && $shortcodeArgs['collection_slugs']) {
				$shortcode_args['custom']['collection_slugs'] = $shortcodeArgs['collection_slugs'];
			}

			//
			// Limit
			//
			if (isset($shortcodeArgs['limit']) && $shortcodeArgs['limit']) {
				$shortcode_args['custom']['limit'] = $shortcodeArgs['limit'];
			}

			//
			// Items per row
			//
			if (isset($shortcodeArgs['items-per-row']) && $shortcodeArgs['items-per-row']) {
				$shortcode_args['custom']['items-per-row'] = $shortcodeArgs['items-per-row'];
			}

			//
			// Pagination
			//
			if (isset($shortcodeArgs['pagination'])) {
				$shortcode_args['custom']['pagination'] = false;
			}

			//
			// Page
			//
			if (isset($shortcodeArgs['page']) && $shortcodeArgs['page']) {
				$shortcode_args['paged'] = $shortcodeArgs['page'];
			}

			//
			// Add to cart
			//
			if (isset($shortcodeArgs['add-to-cart']) && $shortcodeArgs['add-to-cart']) {
				$shortcode_args['custom']['add-to-cart'] = $shortcodeArgs['add-to-cart'];
			}

			//
			// Breadcrumbs
			//
			if (isset($shortcodeArgs['breadcrumbs']) && $shortcodeArgs['breadcrumbs']) {
				$shortcode_args['custom']['breadcrumbs'] = $shortcodeArgs['breadcrumbs'];
			}

			//
			// Keep permalinks
			//
			if (isset($shortcodeArgs['keep-permalinks']) && $shortcodeArgs['keep-permalinks']) {
				$shortcode_args['custom']['keep-permalinks'] = $shortcodeArgs['keep-permalinks'];
			}

			return $shortcode_args;

		}


		/*

		Map collections shortcode arguments

		Defines the available shortcode arguments by checking
		if they exist and applying them to the custom property.

		The returned value eventually gets passed to wps_clauses_mod

		*/
		public static function map_collections_args_to_query($shortcodeArgs) {

			$query = array(
				'post_type'         => WPS_COLLECTIONS_POST_TYPE_SLUG,
				'post_status'       => 'publish',
				'paged'             => 1
			);

			//
			// Order
			//
			if (isset($shortcodeArgs['order']) && $shortcodeArgs['order']) {
				$shortcode_args['custom']['order'] = $shortcodeArgs['order'];
			}

			//
			// Order by
			//
			if (isset($shortcodeArgs['orderby']) && $shortcodeArgs['orderby']) {
				$shortcode_args['custom']['orderby'] = $shortcodeArgs['orderby'];
			}

			//
			// IDs
			//
			if (isset($shortcodeArgs['ids']) && $shortcodeArgs['ids']) {
				$shortcode_args['custom']['ids'] = $shortcodeArgs['ids'];
			}

			//
			// Meta Slugs
			//
			if (isset($shortcodeArgs['slugs']) && $shortcodeArgs['slugs']) {
				$shortcode_args['custom']['slugs'] = $shortcodeArgs['slugs'];
			}

			//
			// Meta Title
			//
			if (isset($shortcodeArgs['titles']) && $shortcodeArgs['titles']) {
				$shortcode_args['custom']['titles'] = $shortcodeArgs['titles'];
			}

			//
			// Descriptions
			//
			if (isset($shortcodeArgs['desc']) && $shortcodeArgs['desc']) {
				$shortcode_args['custom']['desc'] = $shortcodeArgs['desc'];
			}

			//
			// Limit
			//
			if (isset($shortcodeArgs['limit']) && $shortcodeArgs['limit']) {
				$shortcode_args['custom']['limit'] = $shortcodeArgs['limit'];
			}

			//
			// Items per row
			//
			if (isset($shortcodeArgs['items-per-row']) && $shortcodeArgs['items-per-row']) {
				$shortcode_args['custom']['items-per-row'] = $shortcodeArgs['items-per-row'];
			}

			//
			// Pagination
			//
			if (isset($shortcodeArgs['pagination'])) {
				$shortcode_args['custom']['pagination'] = false;
			}

			//
			// Breadcrumbs
			//
			if (isset($shortcodeArgs['breadcrumbs']) && $shortcodeArgs['breadcrumbs']) {
				$shortcode_args['custom']['breadcrumbs'] = $shortcodeArgs['breadcrumbs'];
			}

			//
			// Keep permalinks
			//
			if (isset($shortcodeArgs['keep-permalinks']) && $shortcodeArgs['keep-permalinks']) {
				$shortcode_args['custom']['keep-permalinks'] = $shortcodeArgs['keep-permalinks'];
			}

			return $shortcode_args;

		}


	  /*

	  Formats products shortcode args
	  Returns SQL query

	  TODO: Combine with wps_format_collections_shortcode_args

	  */
	  public static function wps_format_products_shortcode_args($shortcodeArgs) {

	    if ( isset($shortcodeArgs) && $shortcodeArgs ) {

	      foreach ($shortcodeArgs as $key => $arg) {

	        if (strpos($arg, ',') !== false) {
	          $shortcodeArgs[$key] = self::wps_comma_list_to_array( trim($arg) );

	        } else {
	          $shortcodeArgs[$key] = trim($arg);

	        }

	      }

	      $productsQuery = self::map_products_args_to_query($shortcodeArgs);

	      return $productsQuery;


	    } else {
	      return array();

	    }

	  }


	  /*

	  Formats collections shortcode args
	  Returns SQL query

	  TODO: Combine with wps_format_products_shortcode_args

	  */
		public static function wps_format_collections_shortcode_args($shortcodeArgs) {

	    if ( isset($shortcodeArgs) && $shortcodeArgs ) {

	      foreach ($shortcodeArgs as $key => $arg) {

	        if (strpos($arg, ',') !== false) {
	          $shortcodeArgs[$key] = self::wps_comma_list_to_array( trim($arg) );

	        } else {
	          $shortcodeArgs[$key] = trim($arg);

	        }

	      }

	      $collectionsQuery = self::map_collections_args_to_query($shortcodeArgs);
	      return $collectionsQuery;

	    } else {
	      return array();

	    }


		}


		/*

		Turns comma seperated list into array

		*/
		public static function wps_comma_list_to_array($string) {
	    return array_map('trim', explode(',', $string));
		}


	  /*

		Removes duplicates

		*/
		public static function wps_remove_duplicates($collectionIDs) {

	    $dups = array();

	    foreach ( array_count_values($collectionIDs) as $collection => $ID ) {

	      if ($ID > 1) {
	        $dups[] = $collection;
	      }

		  }

	    return $dups;

	  }


	  /*

	  Delete product data from database

	  */
	  public static function wps_delete_product_data($postID, $type, $dataToDelete) {

	  	foreach ($dataToDelete as $key => $value) {
	  		delete_post_meta($postID, $type, $value);
	  	}

	  }


	  /*

	  Add product data to database

	  */
	  public static function wps_add_product_data($postID, $type, $dataToAdd) {

	    foreach ($dataToAdd as $key => $value) {
	      add_post_meta($postID, $type, $value);
	    }

	  }


	  /*

	  Return product collections

	  */
	  public static function wps_return_product_collections($collects) {

	    $collectionIDs = array();

	    foreach ($collects as $key => $value) {
	      array_push($collectionIDs, $collects[$key]->collection_id);
	    }

	    return $collectionIDs;

	  }


	  /*

	  Find existing products

	  */
	  public static function wps_find_existing_products() {

	    $existingProducts = array();

	    $posts = get_posts(array(
	      'posts_per_page'   => -1,
	      'post_type'        => WPS_PRODUCTS_POST_TYPE_SLUG,
	      'post_status'      => 'publish'
	    ));

	    foreach ($posts as $post) {
				$existingProducts[$post->ID] = $post->post_name;
			}

	    return $existingProducts;

	  }


	  /*

	  Get collection ID by Handle

	  */
	  public static function wps_get_collection_id_by_handle($handle) {

	    $args = array(
	      'post_type' => WPS_COLLECTIONS_POST_TYPE_SLUG,
	      'post_status' => 'publish',
	      'posts_per_page' -1,
	      'meta_query' => array(
	        array(
	          'key'    => 'wps_collection_handle',
	          'value'  => $handle
	        )
	      )
	    );

	    $collection = get_posts($args);


	    if(isset($collection) && $collection) {
	      $collectionID = get_post_meta( $collection[0]->ID, 'wps_collection_id', true );
	      return $collectionID;

	    } else {
	      return false;
	    }

	  }


	  /*

	  Construct Products Args

	  */
	  public function wps_construct_products_args() {

	    /*

	    Check what was passed in and contruct our arguments for WP_Query

	    */
	    if( isset($wps_shortcode_atts['collections']) && $wps_shortcode_atts['collections']) {

	      // Removing all spaces
	      // $collections = Utils::wps_remove_spaces_from_string($wps_shortcode_atts['collections']);

	      // If user passed in collection as handle, find ID version
	      if(!ctype_digit($wps_shortcode_atts['collections'])) {
	        $collections = Utils::wps_get_collection_id_by_handle($wps_shortcode_atts['collections']);
	      } else {
	        $collections = $wps_shortcode_atts['collections'];
	      }


	      // $collectionIDs = self::wps_comma_list_to_array($collections);

	      $args = array(
	        'post_type' => WPS_PRODUCTS_POST_TYPE_SLUG,
	        'post_status' => 'publish',
	        'posts_per_page' => $wps_shortcode_atts['limit'] ? $wps_shortcode_atts['limit'] : -1,
	        'paged' => $paged,
	        'meta_query' => array(
	          array(
	            'key'    => 'wps_product_collections',
	            'value'  => $collections
	          )
	        )
	      );

	    } else {

	      if( isset($wps_shortcode_atts['products']) && $wps_shortcode_atts['products'] ) {
	        $products = Utils::wps_remove_spaces_from_string($wps_shortcode_atts['products']);
	        $productIDs = self::wps_comma_list_to_array($products);

	        $args = array(
	          'post__in' => $productIDs,
	          'post_type' => WPS_PRODUCTS_POST_TYPE_SLUG,
	          'post_status' => 'publish',
	          'paged' => $paged,
	          'posts_per_page' => $wps_shortcode_atts['limit']
	        );

	      } else {

	        $args = array(
	          'post_type' => WPS_PRODUCTS_POST_TYPE_SLUG,
	          'post_status' => 'publish',
	          'paged' => $paged,
	          'posts_per_page' => $wps_shortcode_atts['limit']
	        );

	      }

	    }

	  }


		/*

		Checks if needle exists in associative array

		*/
		public static function in_assoc($needle, $array) {

			$key = array_keys($array);
	    $value = array_values($array);

	    if (in_array($needle,$key)) {
				return true;

			} elseif (in_array($needle,$value)) {
				return true;

			} else {
				return false;

			}

		}


		/*

		Responsible for checking whether a variant is available for
		purchase.  must be an (object)

		$variant is expected to have the following properties:

		$variant->inventory_management
		$variant->inventory_quantity
		$variant->inventory_policy

		*/
		public static function is_available_to_buy($variant) {

			if ( !is_object($variant) ) {
				$variant = self::convert_array_to_object($variant);
			}

			// User has set Shopify to track the product's inventory
			if ($variant->inventory_management === 'shopify') {

				// If the product's inventory is 0 or less than 0
				if ($variant->inventory_quantity <= 0) {

					// If 'Allow customers to purchase this product when it's out of stock' is unchecked
					if ($variant->inventory_policy === 'deny') {

						return false;

					} else {
						return true;
					}

				} else {
					return true;
				}

			// User has set product to "do not track inventory" (always able to purchase)
			} else {
				return true;

			}

		}


	  /*

	  Product Inventory
		Checks whether a product's variant(s) are in stock or not

	  */
	  public static function product_inventory($product, $variants = false) {

			$product = self::convert_array_to_object($product);

			if ($variants) {
				return array_values( array_filter($variants, [__CLASS__, 'is_available_to_buy']) );
			}

			if (!self::has($product, 'variants')) {
				return [];
			}

			return array_values( array_filter($product->variants, [__CLASS__, 'is_available_to_buy']) );

	  }


	  /*

	  Construct Option Selections

	  */
	  public static function construct_option_selections($selectedOptions) {

	    $newSelectedOptions = $selectedOptions;
	    $indexx = 1;

	    foreach ($newSelectedOptions as $key => $optionVal) {

				// stripcslashes is import incase user has quotes within variant name
	      $newSelectedOptions['option' . $indexx] = stripcslashes($optionVal);
	      $indexx++;

	      unset($newSelectedOptions[$key]);

	    }

	    return $newSelectedOptions;

	  }


	  /*

	  Filter Variants To Options Values

	  */
	  public static function filter_variants_to_options_values($variants) {

			$variants = self::convert_object_to_array($variants);

	    return array_map(function($variant) {

				$variant = (array) $variant;

	      return array_filter($variant, function($k, $v) {

	        return strpos($v, 'option') !== false;

	      }, ARRAY_FILTER_USE_BOTH );

	    }, $variants);

	  }


	  /*

	  Generic function to sort by a specific key / value

	  */
	  public static function wps_sort_by($array, $key) {

			$array = self::convert_object_to_array($array);

	    usort($array, function($a, $b) use (&$key) {

				$a = self::convert_object_to_array($a);
				$b = self::convert_object_to_array($b);

	      $a = $a[$key];
	      $b = $b[$key];

	      if ($a == $b) return 0;
	      return ($a < $b) ? -1 : 1;

	    });

			return self::convert_array_to_object($array);

	  }


	  /*

	  Generic function to sort by a specific key / value

	  */
	  public static function shift_arrays_up($array) {

			$newArray = [];

			foreach ($array as $index => $countArray) {

				foreach ($countArray as $name => $count) {
					$newArray[$name] = $count;
				}

			}

			return $newArray;

	  }


		/*

	  Generic function to sort by a specific key / value

	  */
	  public static function get_current_page($postVariables) {

			if (!isset($postVariables['currentPage']) || !$postVariables['currentPage']) {
				$currentPage = 1;

			} else {
				$currentPage = $postVariables['currentPage'];
			}

			return $currentPage;

	  }


		/*

		Gets the number of button columns per product

		*/
		public static function get_product_button_width($product) {

			if (count($product->options) === 1) {

			  if (count($product->variants) > 1) {
			    $col = 2;

			  } else {
			    $col = 1;
			  }

			} else if (count($product->options) === 2) {
			  $col = 1;

			} else if (count($product->options) === 3) {
			  $col = 1;

			} else {
			  $col = 1;
			}

			return $col;

		}


		/*

		Ensures scripts don't timeout

		*/
		public static function prevent_timeouts() {

			if ( !function_exists('ini_get') || !ini_get('safe_mode') ) {
				@set_time_limit(0);
			}

		}


		/*

		Check is Object has a property

		*/
		public static function has($block, $property) {
			return is_object($block) && property_exists($block, $property) ? true : false;
		}


		/*

		Checks if item is NOT an empty array

		*/
		public static function array_not_empty($maybe_array) {

			if (is_array($maybe_array) && !empty($maybe_array)) {
				return true;

			} else {
				return false;
			}

		}


		/*

		Checks if item is an empty array

		*/
		public static function array_is_empty($maybe_array) {

			if (is_array($maybe_array) && empty($maybe_array)) {
				return true;

			} else {
				return false;
			}

		}


		/*

		If the product or collection has the Online Sales channel enabled ...

		If published_at is null, we know the user turned off the Online Store sales channel.
		TODO: Shopify may implement better sales channel checking in the future API. We should
		then check for Buy Button visibility as-well.

		*/
		public static function is_data_published($item) {

			if (property_exists($item, 'published_at') && $item->published_at !== null) {
				return true;

			} else {
				return false;
			}

		}


		/*

		Wraps something with an array

		*/
		public static function wrap_in_array($something) {

			if (!is_array($something)) {
				$something = [$something];
			}

			return $something;

		}


		/*

		Runs for every insertion and update to to DB

		*/
		public static function convert_needed_values_to_datetime($data_array) {

			$data_array = self::convert_object_to_array($data_array);

			foreach ($data_array as $key => $value) {

				switch ($key) {

					case 'created_at':
						$data_array[$key] = self::convert_string_to_datetime($value);
						break;

					case 'updated_at':
						$data_array[$key] = self::convert_string_to_datetime($value);
						break;

					case 'published_at':
						$data_array[$key] = self::convert_string_to_datetime($value);
						break;

					case 'closed_at':
						$data_array[$key] = self::convert_string_to_datetime($value);
						break;

					case 'cancelled_at':
						$data_array[$key] = self::convert_string_to_datetime($value);
						break;

					case 'processed_at':
						$data_array[$key] = self::convert_string_to_datetime($value);
						break;

					case 'expires':
						$data_array[$key] = self::convert_string_to_datetime($value);
						break;

					default:
						break;
				}

			}

			return $data_array;

		}


		/*

		Converts a string to datetime

		*/
		public static function convert_string_to_datetime($date_string) {

			if (is_string($date_string)) {
				return date("Y-m-d H:i:s", strtotime($date_string));

			} else {
				return $date_string;
			}

		}


		/*

		Converts a url to protocol relative

		*/
		public static function convert_to_relative_url($url) {

			if (strpos($url, '://') === false) {
			  return $url;

			} else {
				return '//' . explode("://", $url)[1];
			}

		}


		/*

		Converts a url to HTTPS

		*/
		public static function convert_to_https_url($url) {

			if (strpos($url, '://') === false) {
			  return $url;

			} else {
				return 'https://' . explode("://", $url)[1];
			}

		}


		/*

		Removes object properties specified by keys

		*/
		public static function unset_by($object, $keys = []) {

			foreach ($keys as $key) {
				unset($object->{$key});
			}

			return $object;

		}


		/*

		Removes object properties specified by keys

		$item: Represents an object

		*/
		public static function unset_all_except($item, $exception) {

			if (!self::has($item, $exception)) {
				return $item;
			}

			foreach($item as $key => $value) {

				if ($key !== $exception) {
					unset($item->{$key});
				}

			}

			return $item;

		}


		/*

		Filters out any data specified by $criteria

		$items: Represents an array of objects
		$criteria: Represents an array of strings to check object keys by

		*/
		public static function filter_data_by($items, $criteria = []) {

			if (!$criteria) {
				return $items;
			}

			return array_map(function($item) use ($criteria) {
				return self::unset_by($item, $criteria);
			}, $items);

		}


		/*

		Filters out all data NOT specified by $exception

		$items: Represents an array of objects
		$exception: Represents a string to check object keys by

		*/
		public static function filter_data_except($items, $exception = false) {

			if (!$exception) {
				return $items;
			}

			return array_map(function($item) use ($exception) {
				return self::unset_all_except($item, $exception);
			}, $items);

		}


	}

}

<?php

namespace WPS;

require plugin_dir_path( __FILE__ ) . '../vendor/autoload.php';

use WPS\Backend;
use WPS\DB\Settings_Connection;
use WPS\DB\Settings_General;
use WPS\DB\Settings_License;
use WPS\DB\Shop;
use WPS\DB\Products;
use WPS\DB\Variants;
use WPS\DB\Collects;
use WPS\Transients;
use WPS\DB\Options;
use WPS\DB\Collections_Custom;
use WPS\DB\Collections_Smart;
use WPS\DB\Images;
use WPS\DB\Tags;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;


use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;






/*

Class Web Service

*/
class WS {

  protected static $instantiated = null;

  private $Config;

	/*

	Initialize the class and set its properties.

	*/
	public function __construct($Config) {
		$this->config = $Config;
    $this->connection = $this->config->wps_get_settings_connection();
    $this->connection_option_name = $this->config->settings_connection_option_name;

    $this->general = $this->config->wps_get_settings_general();
    $this->general_option_name = $this->config->settings_general_option_name;
	}


  /*

	Creates a new class if one hasn't already been created.
	Ensures only one instance is used.

	*/
	public static function instance() {

		if (is_null(self::$instantiated)) {
			self::$instantiated = new self();
		}

		return self::$instantiated;

	}


  /*

  Get Products Count

  */
  public function wps_ws_get_products_count() {

    $url = "https://" . $this->connection->domain . "/admin/products/count.json";

    // Unit test URL
    // $url = 'http://www.mocky.io/v2/59752ba31000003c071bc37e';

    $headers = array(
      'X-Shopify-Access-Token' => $this->connection->access_token
    );

    try {

      $Guzzle = new Guzzle();
      $guzzelResponse = $Guzzle->request('GET', $url, array(
        'headers' => $headers
      ));

      $productsCountResponse = json_decode($guzzelResponse->getBody()->getContents());

      if (is_object($productsCountResponse) && property_exists($productsCountResponse, 'count')) {
        wp_send_json_success($productsCountResponse);

      } else {
        wp_send_json_error($productsCountResponse);
      }

    } catch (\InvalidArgumentException $error) {

      wp_send_json_error($error->getMessage());

    } catch (RequestException $error) {

      $responseDecoded = json_decode($error->getResponse()->getBody()->getContents());

      wp_send_json_error($responseDecoded->errors);

    } catch (ClientException $error) {

      $responseDecoded = json_decode($error->getResponse()->getBody()->getContents());

      wp_send_json_error($responseDecoded->errors);

    // Server errors 5xx
    } catch (ServerException $error) {

      $responseDecoded = json_decode($error->getResponse()->getBody()->getContents());

      wp_send_json_error($responseDecoded->errors);

    }


  }


  /*

  Get Collections Count

  */
  public function wps_ws_get_collects_count() {

    $url = "https://" . $this->connection->domain . "/admin/collects/count.json";

    $headers = array(
      'X-Shopify-Access-Token' => $this->connection->access_token
    );

    try {

      $Guzzle = new Guzzle();
      $guzzelResponse = $Guzzle->request('GET', $url, array(
        'headers' => $headers
      ));

      $collectionsCountResponse = json_decode($guzzelResponse->getBody()->getContents());

      if (is_object($collectionsCountResponse) && property_exists($collectionsCountResponse, 'count')) {
        wp_send_json_success($collectionsCountResponse);

      } else {
        wp_send_json_error($collectionsCountResponse);

      }

    } catch (RequestException $error) {

      $responseDecoded = json_decode($error->getResponse()->getBody()->getContents());

      wp_send_json_error($responseDecoded->errors);

    }


  }


  /*

  Get Shop Data

  */
  public function wps_ws_get_shop_data() {

    $url = "https://" . $this->connection->domain . "/admin/shop.json";

    $headers = array(
      'X-Shopify-Access-Token' => $this->connection->access_token
    );

    try {

      $Guzzle = new Guzzle();
      $guzzelResponse = $Guzzle->request('GET', $url, array(
        'headers' => $headers
      ));

      $shopDataResponse = json_decode($guzzelResponse->getBody()->getContents());

      if (is_object($shopDataResponse) && property_exists($shopDataResponse, 'shop')) {
        wp_send_json_success($shopDataResponse);

      } else {
        wp_send_json_error($shopDataResponse);

      }


    } catch (RequestException $error) {

      $responseDecoded = json_decode($error->getResponse()->getBody()->getContents());

      wp_send_json_error($responseDecoded->errors);

    }



  }









  /*

  Get Products + Variants

  Here we make our requests to the API to insert products and variants

  */
  public function wps_insert_products_data() {

    $DB_Variants = new Variants();
    $DB_Products = new Products();
    $DB_Options = new Options();
    $DB_Images = new Images();

    if(!isset($_POST['currentPage']) || !$_POST['currentPage']) {
      $currentPage = 1;

    } else {
      $currentPage = $_POST['currentPage'];
    }

    $url = "https://" . $this->connection->domain . "/admin/products.json?limit=250&page=" . $currentPage;

    /*

    If Access Token is expired or wrong the follow error will result:
    "[API] Invalid API key or access token (unrecognized login or wrong password)"

    */
    $headers = array(
      'X-Shopify-Access-Token' => $this->connection->access_token
    );


    try {

      $Guzzle = new Guzzle();
      $guzzelResponse = $Guzzle->request('GET', $url, array(
        'headers' => $headers
      ));

      $data = json_decode($guzzelResponse->getBody()->getContents());

      // $data = false;
      if (property_exists($data, "products")) {

        /*

        This is where the bulk of product data is inserted into the database. The
        "insert_products" method inserts both the CPT's and custom WPS table data.

        */
        $resultProducts = $DB_Products->insert_products( $data->products );
        $resultVariants = $DB_Variants->insert_variants( $data->products );
        $resultOptions = $DB_Options->insert_options( $data->products );
        $resultImages = $DB_Images->insert_images( $data->products );

        echo json_encode($data->products);
        die();

      } else {

        echo json_encode($data->errors);
        die();

      }


    } catch (RequestException $error) {

      echo json_decode($error->getResponse()->getBody()->getContents())->errors;
      die();

    }


  }


  /*

  Get Variants

  */
  public function wps_ws_get_variants() {

    $productID = $_POST['productID'];

    if(!isset($_POST['currentPage']) || !$_POST['currentPage']) {
      $currentPage = 1;

    } else {
      $currentPage = $_POST['currentPage'];
    }

    if (empty($productID)) {
      return false;

    } else {

      try {

        $url = "https://" . $this->connection->domain . "/admin/products/" . $productID . "/variants.json?limit=250&page=" . $currentPage;

        $headers = array(
          'X-Shopify-Access-Token' => $this->connection->access_token
        );

        $Guzzle = new Guzzle();
        $guzzelResponse = $Guzzle->request('GET', $url, array(
          'headers' => $headers
        ));

        $data = json_decode($guzzelResponse->getBody()->getContents());

        if (property_exists($data, 'variants')) {
          echo json_encode($data);
          die();

        } else {
          echo json_encode($data->errors);
          die();

        }


      } catch (RequestException $error) {

        echo json_decode($error->getResponse()->getBody()->getContents())->errors;
        die();

      }

    }

  }


  /*

  Get Collections

  */
  public function wps_insert_custom_collections_data() {

    $DB_Collections_Custom = new Collections_Custom();

    try {

      $url = "https://" . $this->connection->domain . "/admin/custom_collections.json";

  		$headers = array(
  			'X-Shopify-Access-Token' => $this->connection->access_token
  		);

      $Guzzle = new Guzzle();
      $guzzelResponse = $Guzzle->request('GET', $url, array(
        'headers' => $headers
      ));

      $data = json_decode($guzzelResponse->getBody()->getContents());

      if (property_exists($data, "custom_collections")) {

        $results = $DB_Collections_Custom->insert_custom_collections( $data->custom_collections );

        /*

        Once this point is reached, all the data has been synced.
        set_transident allows for /products and /collections permalinks to work

        TODO: Make this more clear from within JS side
        TODO: Modularize

        */
        set_transient('wps_settings_updated', true);
        Transients::check_rewrite_rules();
        Transients::delete_cached_connections();

        echo json_encode($results);
        die();

      } else {

        echo json_encode($data->errors);
        die();

      }


    } catch (RequestException $error) {

      echo json_decode($error->getResponse()->getBody()->getContents())->errors;
      die();

    }


	}


  /*

  Get Collections

  */
  public function wps_insert_smart_collections_data() {

    $DB_Collections_Smart = new Collections_Smart();

    try {

      $url = "https://" . $this->connection->domain . "/admin/smart_collections.json";

  		$headers = array(
  			'X-Shopify-Access-Token' => $this->connection->access_token
  		);

      $Guzzle = new Guzzle();
      $guzzelResponse = $Guzzle->request('GET', $url, array(
        'headers' => $headers
      ));

      $data = json_decode($guzzelResponse->getBody()->getContents());

      if (property_exists($data, "smart_collections")) {
        $results = $DB_Collections_Smart->insert_smart_collections( $data->smart_collections );

        echo json_encode($results);
    		die();

      } else {

        echo json_encode($data->errors);
        die();

      }


    } catch (RequestException $error) {

      echo json_decode($error->getResponse()->getBody()->getContents())->errors;
      die();

    }


	}


  /*

  Get products from collection

  */
  public function wps_ws_get_products_from_collection() {

    try {

      $collectionID = $_POST['collectionID'];

      $url = "https://" . $this->connection->domain . "/admin/products.json?collection_id=" . $collectionID;

      $headers = array(
        'X-Shopify-Access-Token' => $this->connection->access_token
      );

      $Guzzle = new Guzzle();
      $guzzelResponse = $Guzzle->request('GET', $url, array(
        'headers' => $headers
      ));

      $data = json_decode($guzzelResponse->getBody()->getContents());

      if (property_exists($data, 'products')) {
        echo json_encode($data);
        die();

      } else {
        echo json_encode($data->errors);
        die();

      }

    } catch (RequestException $error) {

      echo json_decode($error->getResponse()->getBody()->getContents())->errors;
      die();

    }


  }


  /*

  Get a list of collects by product ID

  */
  public function wps_insert_collects() {


    $DB_Collects = new Collects();

    if(!isset($_POST['currentPage']) || !$_POST['currentPage']) {
      $currentPage = 1;

    } else {
      $currentPage = $_POST['currentPage'];
    }


    try {

      $url = "https://" . $this->connection->domain . "/admin/collects.json?limit=250&page=" . $currentPage;

      $headers = array(
        'X-Shopify-Access-Token' => $this->connection->access_token
      );

      $Guzzle = new Guzzle();
      $guzzelResponse = $Guzzle->request('GET', $url, array(
        'headers' => $headers
      ));

      $data = json_decode($guzzelResponse->getBody()->getContents());

      if (property_exists($data, "collects")) {

        $resultProducts = $DB_Collects->insert_collects( $data->collects );
        echo json_encode($data->collects);
        die();

      } else {

        echo json_encode($data->errors);
        die();

      }


    } catch (RequestException $error) {

      echo json_decode($error->getResponse()->getBody()->getContents())->errors;
      die();

    }


  }


  /*

	Get a list of collects by product ID

	*/
	public function wps_ws_get_collects_from_product($productID = null) {

    $ajax = true;

    if ($productID === null) {
      $productID = $_POST['productID'];

    } else {
      $ajax = false;
    }

    try {

      $url = "https://" . $this->connection->domain . "/admin/collects.json?product_id=" . $productID;

      $headers = array(
        'X-Shopify-Access-Token' => $this->connection->access_token
      );

      $Guzzle = new Guzzle();
      $guzzelResponse = $Guzzle->request('GET', $url, array(
        'headers' => $headers
      ));

      $data = json_decode($guzzelResponse->getBody()->getContents());

      if (property_exists($data, 'collects')) {

        if ($ajax) {
          echo json_encode($data);
          die();

        } else {
          return $data;

        }

      } else {
        echo json_encode($data->errors);
        die();

      }


    } catch (RequestException $error) {

      echo json_decode($error->getResponse()->getBody()->getContents())->errors;
      die();

    }


	}


  /*

  Get a list of collects by collection ID

  */
  public function wps_ws_get_collects_from_collection($collectionID = null) {

    $ajax = true;

    if ($collectionID === null) {
      $collectionID = $_POST['collectionID'];

    } else {
      $ajax = false;
    }

    try {

      $url = "https://" . $this->connection->domain . "/admin/collects.json?collection_id=" . $collectionID;

      $headers = array(
        'X-Shopify-Access-Token' => $this->connection->access_token
      );

      $Guzzle = new Guzzle();
      $guzzelResponse = $Guzzle->request('GET', $url, array(
        'headers' => $headers
      ));

      $data = json_decode($guzzelResponse->getBody()->getContents());

      if (property_exists($data, 'collects')) {

        if ($ajax) {
          echo json_encode($data);
          die();

        } else {
          return $data;

        }

      } else {
        echo json_encode($data->errors);
        die();

      }

    } catch (RequestException $error) {

      echo json_decode($error->getResponse()->getBody()->getContents())->errors;
      die();

    }


  }


  /*

  Get single collection

  */
  public function wps_ws_get_single_collection() {

    try {

      $collectionID = $_POST['collectionID'];

      $url = "https://" . $this->connection->domain . "/admin/custom_collections/" . $collectionID . ".json";

      $headers = array(
        'X-Shopify-Access-Token' => $this->connection->access_token
      );

      $Guzzle = new Guzzle();
      $guzzelResponse = $Guzzle->request('GET', $url, array(
        'headers' => $headers
      ));

      $data = json_decode($guzzelResponse->getBody()->getContents());

      if (property_exists($data, 'custom_collection')) {
        echo json_encode($data);
        die();

      } else {
        echo json_encode($data->errors);
        die();

      }

    } catch (RequestException $error) {

      echo json_decode($error->getResponse()->getBody()->getContents())->errors;
      die();

    }


  }


  /*

  Invalidate the Shopify API connection

  */
  public function wps_ws_end_api_connection() {

    if(property_exists($this->connection, 'access_token') && $this->connection->access_token) {

      try {

        $url = "https://" . $this->connection->domain . "/admin/api_permissions/current.json";

        $headers = array(
          "Content-Type" => "application/json",
          "Accept" => "application/json",
          "Content-Length" => "0",
          "X-Shopify-Access-Token" => $this->connection->access_token
        );

        $Guzzle = new Guzzle();
        $guzzelResponse = $Guzzle->delete($url, array(
          'headers' => $headers
        ));

        return true;

      } catch (RequestException $error) {

        $errorResp = json_decode($error->getResponse()->getBody()->getContents())->errors;

        return new \WP_Error('error', $errorResp);

      }

    } else {

      return new \WP_Error('error', 'Unable to disconnect Shopify store. Missing or invalid access token');

    }

  }


  /*

  When the user is sent back to their site ...

  TODO: More effective way to do this?

  At this point we know that all the validation and authorization checks
  have passed (because auth=true). We can now get our Shopify access token.
  However before we can do that, we need to collect all the values that we'll
  need for the call. Those values are ...

    1. code
    2. api key
    3. shared secret
    4. shop

  We also need to verify that the shop domain that is passed into the URL
  parameters has a corrosponding code (generated via shopify). We can access
  this data from the WP Shopify database. To find the code, we'll do a lookup
  by shop domain AND nonce.

  */
  public function wps_ws_on_authorization() {

    if(isset($_GET["auth"]) && trim($_GET["auth"]) == 'true') {

      $WPS_Waypoint = new Waypoints($this->config);
      $WPS_Webhooks = new Webhooks($this->config);

      $waypointSettings = json_decode( $WPS_Waypoint->wps_waypoint_settings() );

      $waypointClients = $WPS_Waypoint->wps_waypoint_clients( $WPS_Waypoint->wps_waypoint_auth() );

      $matchedWaypointClient = $WPS_Waypoint->wps_waypoint_filter_clients($waypointClients);

      $accessTokenData = Utils::wps_construct_access_token_data($matchedWaypointClient, $waypointSettings);


      // Get Shopify Access Token
      $token = $WPS_Waypoint->wps_waypoint_get_access_token($accessTokenData);

      // Save Shopify Access Token
      $WPS_Waypoint->wps_waypoint_save_access_token($token);

      // Registers all webhooks.
      $WPS_Webhooks->wps_webhooks_register();

    }

  }


  /*

	Get Webhooks

	*/
	public function wps_ws_get_webhooks() {

		if (isset($this->connection->domain) && $this->connection->domain) {

      try {

        $url = "https://" . $this->connection->domain . "/admin/webhooks.json";

        $headers = array(
          'X-Shopify-Access-Token' => $this->connection->access_token
        );

        $Guzzle = new Guzzle();
        $guzzelResponse = $Guzzle->request('GET', $url, array(
          'headers' => $headers
        ));

        echo $guzzelResponse->getBody()->getContents();
        die();

      } catch (RequestException $error) {

        echo json_decode($error->getResponse()->getBody()->getContents())->errors;
        die();

      }


		} else {

      echo 'Unable to get webhooks. Domain missing or invalid.';
      die();

		}


	}


  /*

  Delete Webhooks
  TODO: Are we actually deleting? Do we actually need?

  */
  public function wps_ws_delete_webhook() {

    if (isset($this->connection->webhook_id) && $this->connection->webhook_id) {

      try {

        $url = "https://" . $this->connection->domain . "/admin/webhooks/" . $this->connection->webhook_id . ".json";

        $headers = array(
          'X-Shopify-Access-Token' => $this->connection->access_token
        );

        $Guzzle = new Guzzle();
        $guzzelResponse = $Guzzle->request('GET', $url, array(
          'headers' => $headers
        ));

        echo $guzzelResponse->getBody()->getContents();
        die();


      } catch (RequestException $error) {

        echo json_decode($error->getResponse()->getBody()->getContents())->errors;
        die();

      }


    } else {

      $error = new \WP_Error('error', 'No Webhook ID set');

      echo json_encode($error);
      die();

    }

    // Should equal our config settings minus webhook id only
    // $respWP = $this->wps_delete_setting('webhook_id');

    // update_option( $this->plugin_name, $resp );


  }


  function wps_get_progress_count() {

    echo json_encode($_SESSION);
    die();

  }




  public function wps_update_settings_general() {

    global $wp_rewrite;

    $DB_Settings_General = new Settings_General();

    $newGeneralSettings = array();

    if (isset($_POST['wps_settings_general_products_url']) && $_POST['wps_settings_general_products_url']) {
      $newGeneralSettings['url_products'] = $_POST['wps_settings_general_products_url'];
    }

    if (isset($_POST['wps_settings_general_collections_url']) && $_POST['wps_settings_general_collections_url']) {
      $newGeneralSettings['url_collections'] = $_POST['wps_settings_general_collections_url'];
    }

    if (isset($_POST['wps_settings_general_url_webhooks']) && $_POST['wps_settings_general_url_webhooks']) {
      $newGeneralSettings['url_webhooks'] = $_POST['wps_settings_general_url_webhooks'];
    }

    if (isset($_POST['wps_settings_general_num_posts'])) {

      if ($_POST['wps_settings_general_num_posts']) {
        $newGeneralSettings['num_posts'] = $_POST['wps_settings_general_num_posts'];

      } else {
        $newGeneralSettings['num_posts'] = null;

      }

    }

    if (isset($_POST['wps_settings_general_styles_all'])) {
      $newGeneralSettings['styles_all'] = (int)$_POST['wps_settings_general_styles_all'];
    }

    if (isset($_POST['wps_settings_general_styles_core'])) {
      $newGeneralSettings['styles_core'] = (int)$_POST['wps_settings_general_styles_core'];
    }

    if (isset($_POST['wps_settings_general_styles_grid'])) {
      $newGeneralSettings['styles_grid'] = (int)$_POST['wps_settings_general_styles_grid'];
    }

    if (isset($_POST['wps_settings_general_price_with_currency'])) {
      $newGeneralSettings['price_with_currency'] = (int)$_POST['wps_settings_general_price_with_currency'];
    }

    $results = $DB_Settings_General->update_general($newGeneralSettings);


    Transients::delete_cached_settings();
    set_transient('wps_settings_updated', $newGeneralSettings);


    echo json_encode($results);
    die();

  }


  /*

  Reset rewrite rules on CTP url change

  */
  public function wps_reset_rewrite_rules($old_value, $new_value) {
    update_option('rewrite_rules', '');
  }















/*




NEW STRUCTURE






*/













  /*

  Reset rewrite rules on CTP url change

  */
  public function wps_get_connection() {

    if (get_transient('wps_settings_connection')) {
      $connectionData = get_transient('wps_settings_connection');

    } else {

      $DB_Settings_Connection = new Settings_Connection();
      $connectionData = $DB_Settings_Connection->get();
      set_transient('wps_settings_connection', $connectionData);

    }

    echo json_encode($connectionData);
    die();

  }


  /*

  Insert connection data

  */
  public function wps_insert_connection() {

    $DB_Settings_Connection = new Settings_Connection();
    $connectionData = $_POST['connectionData'];

    $results = $DB_Settings_Connection->insert_connection($connectionData);

    echo json_encode($results);
    die();

  }


  /*

  Insert Shop Data

  */
  public function wps_insert_shop() {

    $DB_Shop = new Shop();
    $shopData = $_POST['shopData'];

    $results = $DB_Shop->insert_shop($shopData);

    echo json_encode($results);
    die();

  }





  /*

  Delete Shop Data

  */
  public function wps_delete_shop() {

    $DB_Shop = new Shop();

    if (!$DB_Shop->delete()) {
      return new \WP_Error('error', 'Warning: Unable to delete shop data.');

    } else {
      return true;
    }

  }


  /*

	Delete the config data
  TODO: Support multiple connections by making connection ID dynamic

	*/
	public function wps_delete_settings_connection() {

		$DB_Settings_Connection = new Settings_Connection();

    if (!$DB_Settings_Connection->delete()) {
      return new \WP_Error('error', 'Warning: Unable to delete connection settings.');

    } else {
      return true;
    }

	}


  /*

  Delete the synced Shopify data

  */
  public function wps_delete_synced_data() {

    $Backend = new Backend($this->config);

    if (!$Backend->wps_delete_posts('wps_products')) {
      return new \WP_Error('error', 'Warning: Unable to delete products custom post types.');
    }

    if (!$Backend->wps_delete_posts('wps_collections')) {
      return new \WP_Error('error', 'Warning: Unable to delete collections custom post types.');
    }

    return true;

  }



  /*

  wps_delete_images

  */
  public function wps_delete_images() {

    $Images = new Images();

    if (!$Images->delete()) {
      return new \WP_Error('error', 'Warning: Unable to delete product images.');

    } else {
      return true;
    }

  }


  /*

  wps_delete_images

  */
  public function wps_delete_inventory() {

    $Inventory = new Inventory();

    if (!$Inventory->delete()) {
      return new \WP_Error('error', 'Warning: Unable to delete product inventory.');

    } else {
      return true;
    }

  }



  /*

  wps_delete_collects

  */
  public function wps_delete_collects() {

    $Collects = new Collects();

    if (!$Collects->delete()) {
      return new \WP_Error('error', 'Warning: Unable to delete collects.');

    } else {
      return true;
    }

  }


  /*

  wps_delete_options

  */
  public function wps_delete_tags() {

    $Tags = new Tags();

    if (!$Tags->delete()) {
      return new \WP_Error('error', 'Warning: Unable to delete product tags.');

    } else {
      return true;
    }

  }


  /*

  wps_delete_options

  */
  public function wps_delete_options() {

    $Options = new Options();

    if (!$Options->delete()) {
      return new \WP_Error('error', 'Warning: Unable to delete product options.');

    } else {
      return true;
    }

  }


  /*

  wps_delete_variants

  */
  public function wps_delete_variants() {

    $Variants = new Variants();

    if (!$Variants->delete()) {
      return new \WP_Error('error', 'Warning: Unable to delete product variants.');

    } else {
      return true;
    }

  }


  /*

  wps_delete_products

  */
  public function wps_delete_products() {

    $Products = new Products();

    if (!$Products->delete()) {
      return new \WP_Error('error', 'Warning: Unable to delete products.');

    } else {
      return true;
    }

  }


  /*

  wps_delete_products

  */
  public function wps_delete_custom_collections() {

    $Collections_Custom = new Collections_Custom();

    if (!$Collections_Custom->delete()) {
      return new \WP_Error('error', 'Warning: Unable to delete custom collections.');

    } else {
      return true;
    }

  }


  /*

  wps_delete_products

  */
  public function wps_delete_smart_collections() {

    $Collections_Smart = new Collections_Smart();

    if (!$Collections_Smart->delete()) {
      return new \WP_Error('error', 'Warning: Unable to delete smart collections.');

    } else {
      return true;
    }

  }


  /*


  Drop databases used during uninstall


  */
  public function wps_drop_databases() {

    $DB_Shop = new Shop();
    $DB_Settings_General = new Settings_General();
    $DB_Settings_License = new Settings_License();
    $DB_Settings_Connection = new Settings_Connection();
    $Collections_Smart = new Collections_Smart();
    $Collections_Custom = new Collections_Custom();
    $Products = new Products();
    $Variants = new Variants();
    $Options = new Options();
    $Tags = new Tags();
    $Collects = new Collects();
    $Images = new Images();
    $Transients = new Transients();

    $results['shop'] = $DB_Shop->delete_table();
    $results['settings_general'] = $DB_Settings_General->delete_table();
    $results['settings_license'] = $DB_Settings_License->delete_table();
    $results['settings_connection'] = $DB_Settings_Connection->delete_table();
    $results['collections_smart'] = $Collections_Smart->delete_table();
    $results['collections_custom'] = $Collections_Custom->delete_table();
    $results['products'] = $Products->delete_table();
    $results['variants'] = $Variants->delete_table();
    $results['options'] = $Options->delete_table();
    $results['tags'] = $Tags->delete_table();
    $results['collects'] = $Collects->delete_table();
    $results['images'] = $Images->delete_table();
    $results['transients'] = $Transients->delete_all_cache();

    return $results;

  }


  /*

  Uninstall consumer
  Returns: Response object

  */
  public function wps_uninstall_consumer($ajax = true) {

    /*

    Need to do a few things here ...

    1. Delete all the synced products and collections data
    2. Invalidate the main Shopify API connection:
    3. Remove the wps config values from the database
    4. Delete cache


    TODO: Since invalidating the main Shopify API connection is
    performed asynchronously, we should break that into its own
    request; perhaps after this one.

    Each deletion returns either type boolean of TRUE or a type
    STRING containing the error message.

    */

    if ($_POST['action'] === 'wps_uninstall_consumer') {
      $ajax = true;
    }

    $results = array();
    $Transients = new Transients();
    $DB_Settings_Connection = new Settings_Connection();
    $connection = $DB_Settings_Connection->get_column_single('domain');


    if (!empty($connection)) {

      /*

      Remove API Connection

      */
      $response_connection_api = $this->wps_ws_end_api_connection();

      if (is_wp_error($response_connection_api)) {
        $results['connection_api'] = $response_connection_api->get_error_message();

      } else {
        $results['connection_api'] = $response_connection_api;
      }


      /*

      Remove Shop

      */
      $response_shop = $this->wps_delete_shop();

      if (is_wp_error($response_shop)) {
        $results['shop'] = $response_shop->get_error_message();

      } else {
        $results['shop'] = $response_shop;
      }


      /*

      Remove Connection Settings

      */
      $response_connection_settings = $this->wps_delete_settings_connection();

      if (is_wp_error($response_connection_settings)) {
        $results['connection_settings'] = $response_connection_settings->get_error_message();

      } else {
        $results['connection_settings'] = $response_connection_settings;
      }


      /*

      Remove CPTs

      */
      $response_cpt = $this->wps_delete_synced_data();

      if (is_wp_error($response_cpt)) {
        $results['cpt'] = $response_cpt->get_error_message();

      } else {
        $results['cpt'] = $response_cpt;
      }


      /*

      Remove CPTs

      */
      $response_products = $this->wps_delete_products();

      if (is_wp_error($response_products)) {
        $results['products'] = $response_products->get_error_message();

      } else {
        $results['products'] = $response_products;
      }


      /*

      Remove Custom Collections

      */
      $response_collections_custom = $this->wps_delete_custom_collections();

      if (is_wp_error($response_collections_custom)) {
        $results['custom_collections'] = $response_collections_custom->get_error_message();

      } else {
        $results['custom_collections'] = $response_collections_custom;
      }


      /*

      Remove Smart Collections

      */
      $response_collections_smart = $this->wps_delete_smart_collections();

      if (is_wp_error($response_collections_smart)) {
        $results['smart_collections'] = $response_collections_smart->get_error_message();

      } else {
        $results['smart_collections'] = $response_collections_smart;
      }


      /*

      Remove Collects

      */
      $response_collects = $this->wps_delete_collects();

      if (is_wp_error($response_collects)) {
        $results['collects'] = $response_collects->get_error_message();

      } else {
        $results['collects'] = $response_collects;
      }


      /*

      Remove Variants

      */
      $response_variants = $this->wps_delete_variants();

      if (is_wp_error($response_variants)) {
        $results['variants'] = $response_variants->get_error_message();

      } else {
        $results['variants'] = $response_variants;
      }


      /*

      Remove Options

      */
      $response_options = $this->wps_delete_options();

      if (is_wp_error($response_options)) {
        $results['options'] = $response_options->get_error_message();

      } else {
        $results['options'] = $response_options;
      }


      /*

      Remove Tags

      */
      $response_tags = $this->wps_delete_tags();

      if (is_wp_error($response_tags)) {
        $results['tags'] = $response_tags->get_error_message();

      } else {
        $results['tags'] = $response_tags;
      }


      /*

      Remove Images

      */
      $response_images = $this->wps_delete_images();

      if (is_wp_error($response_images)) {
        $results['images'] = $response_images->get_error_message();

      } else {
        $results['images'] = $response_images;
      }


      /*

      Remove Transients

      */
      $response_transients = $Transients->delete_all_cache();

      if (is_wp_error($response_transients)) {
        $results['transients'] = $response_transients->get_error_message();

      } else {
        $results['transients'] = $response_transients;
      }


    }


    if ($ajax) {

      echo json_encode($results);
      die();

    } else {

      return $results;

    }



  }



}

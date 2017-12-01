import {
  createNewAuthData,
  convertAuthDataToJSON,
  mergeNewDataIntoCurrent,
  convertAuthDataToString,
  controlPromise
} from '../utils/utils-data';


/*

Get all products from Shopify
Returns: Promise

NOT CURRENTLY USED

*/
function uninstallProductData() {

  var options = {
    method: 'POST',
    url: wps.ajax,
    dataType: 'json',
    data: {
      action: 'wps_uninstall_product_data',
      nonce: wps.nonce
    }
  };

  return controlPromise(options);

};


/*

Get all products from Shopify
Returns: Promise

*/
function getProductsCount() {

  var options = {
    method: 'POST',
    url: wps.ajax,
    dataType: 'json',
    data: {
      action: 'wps_ws_get_products_count',
      nonce: wps.nonce
    }
  };

  return controlPromise(options);

};


/*

Get all Collects from Shopify
Returns: Promise

*/
function getCollectsCount() {

  var options = {
    method: 'POST',
    url: wps.ajax,
    dataType: 'json',
    data: {
      action: 'wps_ws_get_collects_count',
      nonce: wps.nonce
    }
  };

  return controlPromise(options);

};


/*

Get Order Count
Returns: Promise

*/
function getOrdersCount() {

  var options = {
    method: 'POST',
    url: wps.ajax,
    dataType: 'json',
    data: {
      action: 'wps_ws_get_orders_count',
      nonce: wps.nonce
    }
  };

  return controlPromise(options);

};


/*

Get Customers Count
Returns: Promise

*/
function getCustomersCount() {

  var options = {
    method: 'POST',
    url: wps.ajax,
    dataType: 'json',
    data: {
      action: 'wps_ws_get_customers_count',
      nonce: wps.nonce
    }
  };

  return controlPromise(options);

};


/*

Get all products from Shopify
Returns: Promise

*/
function getProductsFromCollection(collection) {

  var options = {
    method: 'POST',
    url: wps.ajax,
    dataType: 'json',
    data: {
      action: 'wps_ws_get_products_from_collection',
      collectionID: collection.collectionId,
      nonce: wps.nonce
    }
  };

  return controlPromise(options);

};


/*

Get Collections

*/
function insertCustomCollections() {

  var options = {
    method: 'POST',
    url: wps.ajax,
    dataType: 'json',
    data: {
      action: 'wps_insert_custom_collections_data',
      nonce: wps.nonce
    }
  };

  return controlPromise(options);

};


/*

Get Collections

*/
function insertSmartCollections() {

  var options = {
    method: 'POST',
    url: wps.ajax,
    dataType: 'json',
    data: {
      action: 'wps_insert_smart_collections_data',
      nonce: wps.nonce
    }
  };

  return controlPromise(options);

};


/*

Get all products from Shopify
Returns: Promise

*/
function getCollectsFromProductID(productID) {

  var options = {
    method: 'POST',
    url: wps.ajax,
    dataType: 'json',
    data: {
      action: 'wps_ws_get_collects_from_product',
      productID: productID,
      nonce: wps.nonce
    }
  };

  return controlPromise(options);

};


/*

Get Collects

*/
function insertCollects(currentPage = false) {

  var options = {
    method: 'POST',
    url: wps.ajax,
    dataType: 'json',
    data: {
      action: 'wps_insert_collects',
      currentPage: currentPage,
      nonce: wps.nonce
    }
  };

  return controlPromise(options);

};


/*

Get all products from Shopify
Returns: Promise

*/
function getCollectsFromCollection(collectionID) {

  var options = {
    method: 'POST',
    url: wps.ajax,
    dataType: 'json',
    data: {
      action: 'wps_ws_get_collects_from_collection',
      collectionID: collectionID,
      nonce: wps.nonce
    }
  };

  return controlPromise(options);

};


/*

Get all products from Shopify
Returns: Promise

*/
function getSingleCollection(collectionID) {

  var options = {
    method: 'POST',
    url: wps.ajax,
    dataType: 'json',
    data: {
      action: 'wps_ws_get_single_collection',
      collectionID: collectionID,
      nonce: wps.nonce
    }
  };

  return controlPromise(options);

};


/*

Sending collections to server

*/
function sendCollectionsToServer(collections) {

  var options = {
    method: 'POST',
    url: wps.ajax,
    dataType: 'json',
    data: {
      action: 'wps_insert_collections',
      collections: collections,
      nonce: wps.nonce
    }
  };

  return controlPromise(options);

};


/*

Inserting Shop data

*/
function insertShopData(shopData) {

  var options = {
    method: 'POST',
    url: wps.ajax,
    dataType: 'json',
    data: {
      action: 'wps_insert_shop',
      shopData: shopData,
      nonce: wps.nonce
    }
  };

  return controlPromise(options);

};


/*

Sending collections to server

*/
function insertConnectionData(connectionData) {

  var options = {
    method: 'POST',
    url: wps.ajax,
    dataType: 'json',
    data: {
      action: 'wps_insert_connection',
      connectionData: connectionData,
      nonce: wps.nonce
    }
  };

  return controlPromise(options);

};


/*

Sending collections to server

*/
function getConnectionData() {

  var options = {
    method: 'GET',
    url: wps.ajax,
    dataType: 'json',
    data: {
      action: 'wps_get_connection',
      nonce: wps.nonce
    }
  };

  return controlPromise(options);

};


/*

Insert Products Data

*/
function insertProductsData(currentPage = false) {

  var options = {
    method: 'POST',
    url: wps.ajax,
    dataType: 'json',
    data: {
      action: 'wps_insert_products_data',
      currentPage: currentPage,
      nonce: wps.nonce
    }
  };

  return controlPromise(options);

};


/*

Get Webhooks

*/
function getWebhooks() {

  var options = {
    method: 'POST',
    url: wps.ajax,
    data: {
      action: 'wps_ws_get_webhooks',
      nonce: wps.nonce
    }
  };

  return jQuery.ajax(options);

};


/*

EDD - Get License Key Info
Returns Promise
TODO: Should we remove the hardcoded wpshop.io URL domain?

*/
function getProductInfo(key) {

  var options = {
    type: 'GET',
    url: 'https://wpshop.io/edd-sl?edd_action=get_version&item_name=WP+Shopify&license=' + key + '&url=' + window.location.origin
  };

  return jQuery.ajax(options);

};


/*

EDD - Check License Key Validity
Returns Promise

*/
function getLicenseKeyStatus(key) {

  var options = {
    type: 'GET',
    url: 'https://wpshop.io/edd-sl?edd_action=check_license&item_name=WP+Shopify&license=' + key + '&url=' + window.location.origin
  };

  return jQuery.ajax(options);

};


/*

EDD - Activate License Key
Returns Promise

*/
function activateLicenseKey(key) {

  var options = {
    type: 'GET',
    url: 'https://wpshop.io/edd-sl?edd_action=activate_license&item_name=WP+Shopify&license=' + key + '&url=' + window.location.origin
  };

  return jQuery.ajax(options);

};


/*

EDD - Deactivate License Key
Returns Promise

*/
function deactivateLicenseKey(key) {

  var options = {
    type: 'GET',
    url: 'https://wpshop.io/edd-sl?edd_action=deactivate_license&item_name=WP+Shopify&license=' + key + '&url=' + window.location.origin
  };

  return jQuery.ajax(options);

};


/*

Saving License Key
Returns Promise

*/
function saveLicenseKey(data) {

  data.action = "wps_license_save";
  data.nonce = wps.nonce;

  var options = {
    method: 'POST',
    url: wps.ajax,
    data: data
  };

  return jQuery.ajax(options);

};


/*

Deleting License Key
Returns Promise

*/
function deleteLicenseKey(key) {

  var options = {
    method: 'POST',
    url: wps.ajax,
    data: {
      action: 'wps_license_delete',
      key: key,
      nonce: wps.nonce
    }
  };

  return jQuery.ajax(options);

};


/*

Get License Key
Returns Promise

*/
function getLicenseKey() {

  var options = {
    method: 'GET',
    url: wps.ajax,
    data: {
      action: 'wps_license_get',
      nonce: wps.nonce
    }
  };

  return jQuery.ajax(options);

};


/*

Get the URL needed to send user to Shopify
Returns Promise
TODO: maybe not needed anymore

*/
function getShopifyURL() {

  var options = {
    method: 'POST',
    url: wps.ajax,
    data: {
      action: 'wps_waypoint_get_shopify_url',
      nonce: wps.nonce
    }
  };

  return controlPromise(options);

};


/*

Send uninstall request to Shopify
Returns Promise

*/
function uninstallPlugin() {

  var options = {
    method: 'POST',
    url: wps.ajax,
    dataType: 'json',
    data: {
      action: 'wps_uninstall_consumer',
      nonce: wps.nonce
    }
  };

  return jQuery.ajax(options);

};


/*

Send uninstall request to Shopify
Returns Promise

*/
function removePluginData() {

  var options = {
    method: 'POST',
    url: wps.ajax,
    dataType: 'json',
    data: {
      action: 'wps_uninstall_product_data',
      nonce: wps.nonce
    }
  };

  return jQuery.ajax(options);

};


/*

Set the plugin settings on consumer side. This
allows us to save the plugin settings via AJAX

Returns: Promise

*/
function setPluginSettings(formData) {

  formData.nonce = wps.nonce;

  var options = {
    method: "POST",
    url: "options.php",
    data: formData
  };

  return controlPromise(options);

};


/*

Add webhook

*/
function addWebhook() {

  var options = {
    method: 'POST',
    url: wps.ajax,
    data: {
      action: 'wps_webhooks_register_single',
      nonce: wps.nonce
    }
  };

  return controlPromise(options);

};


/*

Del Webhooks

*/
function delWebhooks() {

  var options = {
    method: 'POST',
    url: wps.ajax,
    data: {
      action: 'wps_ws_delete_webhook',
      nonce: wps.nonce
    }
  };

  return controlPromise(options);

};


/*

Grabbing Shopify credentials from WordPress
Returns: Promise
TODO: Not used

*/
function getShopifyCreds() {

  var options = {
    method: 'GET',
    url: wps.ajax,
    dataType: 'json',
    data: {
      action: 'wps_get_credentials',
      nonce: wps.nonce
    }
  };

  return controlPromise(options);

};


/*

Getting all products
Returns: Promise
TODO: Not used
TODO: Check if controlPromise is needed here

*/
function getAllProducts(shopify) {

  var newnew = shopify.fetchAllProducts();
  return shopify.fetchAllProducts();

};


/*

Get auth token from WP Shopify server:
Returns: Promise

*/
function getAuthToken() {

  var options = {
    method: 'POST',
    url: 'https://wpshop.io/wp-json/jwt-auth/v1/token',
    dataType: "json",
    data: {
      username: 'wp-shopify-auth-user', // TODO: make dynamic?
      password: 'xyWlcxyIwkA(#gUl!Exy$ITz' // TODO: make dynamic?
    }
  };

  return controlPromise(options);

};


/*

Get auth user from WP Shopify server
Returns: Promise

*/
function getAuthUser(token) {

  var options = {
    url: 'https://wpshop.io/wp-json/wp/v2/users/2',
    method: 'GET',
    headers: {
      'Authorization': 'Bearer ' + token
    }
  };

  return controlPromise(options);

};


/*

Get plugin settings from consumer
Returns: Promise

*/
function getPluginSettings() {

  var options = {
    method: 'GET',
    url: wps.ajax,
    dataType: 'json',
    data: {
      action: 'wps_get_options',
      nonce: wps.nonce
    }
  };

  return controlPromise(options);

};


/*

Sending new auth data to WP Shopify server
Returns: Promise

*/
function updateAuthUser(authToken, authUserData) {

  var newAuthData = createNewAuthData(),
      currentAuthData = convertAuthDataToJSON(authUserData.description);

  var finalData = mergeNewDataIntoCurrent(newAuthData, currentAuthData),
      sendable = convertAuthDataToString(finalData);

  var options = {
    url: 'https://wpshop.io/wp-json/wp/v2/users/2', // TODO: Make this dynamic?
    method: 'POST',
    headers: {
      'Authorization': 'Bearer ' + authToken
    },
    data: {
      "description":  sendable
    }
  };

  return controlPromise(options);

};


// function getProgressCookie() {
//
//   var options = {
//     method: 'GET',
//     url: wps.ajax,
//     dataType: 'json',
//     data: {
//       action: 'wps_get_progress_count'
//     }
//   };
//
//   return controlPromise(options);
//
// }


// function getProgressCount() {
//
//
//
//   if (!!window.EventSource) {
//     var source = new EventSource(wps.pluginsPath + '/wp-shopify/admin/partials/wps-sync-progress.php');
//
//     source.addEventListener('message', function(e) {
//
//       // var session = await getProgressCookie();
//       //
//
//       // if (e.origin != window.location.origin) {
//       //   return;
//       // }
//
//     }, false);
//
//     source.addEventListener('open', function(e) {
//       // Connection was opened.
//
//     }, false);
//
//     source.addEventListener('error', function(e) {
//       if (e.readyState == EventSource.CLOSED) {
//       }
//     }, false);
//
//   } else {
//     // Result to xhr polling :(
//
//   }
//
//   return controlPromise(options);
//
// }


/*

updateSettings
Returns: Promise

*/
function updateSettings(options) {

  options.action = 'wps_update_settings_general';
  options.nonce = wps.nonce;

  var options = {
    method: 'POST',
    url: wps.ajax,
    dataType: 'json',
    data: options
  };

  return jQuery.ajax(options);

};


/*

Get plugin settings from consumer
Returns: Promise

*/
function getShopData() {

  var options = {
    method: 'GET',
    url: wps.ajax,
    dataType: 'json',
    data: {
      action: 'wps_ws_get_shop_data',
      nonce: wps.nonce
    }
  };

  return controlPromise(options);

};


/*

Get Product Variants

*/
function getProductVariants(productID) {

  var options = {
    method: 'POST',
    url: wps.ajax,
    dataType: 'json',
    data: {
      action: 'wps_ws_get_variants',
      productID: productID,
      nonce: wps.nonce
    }
  };

  return controlPromise(options);

};


/*

Set Syncing Indicator

*/
function setSyncingIndicator(syncing) {

  var options = {
    method: 'POST',
    url: wps.ajax,
    data: {
      action: 'wps_ws_set_syncing_indicator',
      syncing: syncing,
      nonce: wps.nonce
    }
  };

  return jQuery.ajax(options);

};


/*

Clear Cache

*/
function clearCache() {

  var options = {
    method: 'POST',
    url: wps.ajax,
    data: {
      action: 'wps_clear_cache',
      nonce: wps.nonce
    }
  };

  return controlPromise(options);

}


/*

Sync custom table data with CPT tables

*/
function syncWithCPT() {

  var options = {
    method: 'POST',
    url: wps.ajax,
    data: {
      action: 'wps_sync_with_cpt',
      nonce: wps.nonce
    }
  };

  return controlPromise(options);

}


/*

Insert Orders

*/
function insertOrders(currentPage = false) {

  var options = {
    method: 'POST',
    url: wps.ajax,
    dataType: 'json',
    data: {
      action: 'wps_insert_orders',
      currentPage: currentPage,
      nonce: wps.nonce
    }
  };

  return controlPromise(options);

}


/*

Insert Customers

*/
function insertCustomers(currentPage = false) {

  var options = {
    method: 'POST',
    url: wps.ajax,
    dataType: 'json',
    data: {
      action: 'wps_insert_customers',
      currentPage: currentPage,
      nonce: wps.nonce
    }
  };

  return controlPromise(options);

}


/*

Insert Customers

*/
function startProgressBar(progress) {

  jQuery(document).on('heartbeat-send', (event, data) => {

    console.log("heartbeat-send: ", data);

    // Add additional data to Heartbeat data.
    data.myplugin_customfield = progress;

  });


  jQuery(document).on( 'heartbeat-tick', (event, data) => {

    // Check for our data, and use it.
    if ( ! data.myplugin_customfield_hashed ) {
      return;
    }

    console.log("heartbeat-tick: ", data);
    console.log( 'The hash is ' + data.myplugin_customfield_hashed );

  });

}


export {
  getProductsFromCollection,
  insertCustomCollections,
  insertSmartCollections,
  getCollectsFromProductID,
  getCollectsFromCollection,
  sendCollectionsToServer,
  insertProductsData,
  getWebhooks,
  getShopifyURL,
  uninstallPlugin,
  setPluginSettings,
  addWebhook,
  delWebhooks,
  getAuthToken,
  getAuthUser,
  getPluginSettings,
  updateAuthUser,
  getSingleCollection,
  deactivateLicenseKey,
  activateLicenseKey,
  getLicenseKeyStatus,
  getProductInfo,
  saveLicenseKey,
  deleteLicenseKey,
  getLicenseKey,
  getProductsCount,
  insertCollects,
  getCollectsCount,
  updateSettings,
  getShopData,
  insertShopData,
  insertConnectionData,
  getConnectionData,
  getProductVariants,
  uninstallProductData,
  removePluginData,
  setSyncingIndicator,
  clearCache,
  syncWithCPT,
  insertOrders,
  getOrdersCount,
  insertCustomers,
  getCustomersCount,
  startProgressBar
};

import {
  getConnectionData,
  getShopData,
  getProductsCount,
  getCollectsCount,
  insertConnectionData,
  insertShopData,
  insertProductsData,
  insertCollects,
  insertCustomCollections,
  insertSmartCollections,
  uninstallProductData
} from '../ws/ws';

import {
  connectionInProgress
} from '../ws/localstorage';

import {
  isWordPressError
} from '../utils/utils';

/*

Stream Connection
Returns: Connection

*/
async function streamConnection() {

  return new Promise(async function streamConnectionHandler(resolve, reject) {

    //
    // 1. Get Shop Data
    //
    try {

      var connectionData = await getConnectionData();

      if (isWordPressError(connectionData)) {
        reject(connectionData.data);
      }

      if (!connectionInProgress()) {
        reject('Syncing stopped during streamConnection');
      }

    } catch(error) {
      reject(error);

    }


    //
    // 2. Send to server
    //
    try {

      var connection = await insertConnectionData(connectionData);

      if (!connectionInProgress()) {
        reject('Syncing stopped during streamConnection');
      }

    } catch(error) {
      reject(error);

    }

    resolve(connection);

  });

}


/*

Stream Shop
Returns Shop

*/
async function streamShop() {

  return new Promise(async function streamShopHandler(resolve, reject) {

    //
    // 1. Get Shop Data from Shopify
    // TODO: It's hard to tell at first glance whether we're making a call
    // to Shopify or our internal DB. We should prefix the function to
    // ensure this is clear. Phase 2.
    //
    try {

      var shopData = await getShopData();

      if (typeof shopData === 'string') {
        reject(shopData);
      }

      if (isWordPressError(shopData)) {
        reject(shopData.data);

      } else {
        shopData = shopData.data;
      }

      if (!connectionInProgress()) {
        reject('Syncing stopped during streamShop');
      }

    } catch(error) {
      reject(error);

    }

    //
    // 2. Send to server
    //
    try {

      var shop = await insertShopData(shopData);

      if (isWordPressError(shop)) {
        reject(shop.data);
      }

      if (!connectionInProgress()) {
        reject('Syncing stopped during streamShop');
      }

    } catch(error) {
      reject(error);

    }

    resolve(shop);

  });

}


/*

Stream Products
Returns products

*/
async function streamProducts() {

	var productCount,
      products = [],
      productsCPT,
      pageSize = 250,
      currentPage = 1,
      pages,
      productData;

  return new Promise(async function streamProductsHandler(resolve, reject) {


    //
    // 1. Get products count
    //
    try {

      productCount = await getProductsCount();

      if (isWordPressError(productCount)) {
        reject(productCount.data);

      } else {
        productCount = productCount.data.count;
      }

      if (!connectionInProgress()) {
        reject('Syncing stopped during streamProducts');
      }

    } catch(error) {
      reject(error);

    }

    //
    // 2. Get all products
    // TODO: Abstract out?
    //
    pages = Math.ceil(productCount / pageSize);


    // Run for each page of products
    while(currentPage <= pages) {

      try {

        var newProducts = await insertProductsData(currentPage);

        if (!connectionInProgress()) {
          reject('Syncing stopped during streamProducts');
        }

        if (isWordPressError(newProducts)) {
          reject(newProducts.data);

        } else {

          if (Array.isArray(newProducts.data.products)) {
            products = R.concat(products, newProducts.data.products);
            currentPage += 1;

          } else {
            reject(newProducts.data.products);

          }

        }

      } catch(error) {
        console.error("Error insertProductsData: ", error);

        currentPage = pages+1;
        return reject(error);

      }

    }

    resolve(products);

  });

}


/*

Stream Collects
Returns Collects

*/
async function streamCollects() {

	var collectsCount;

  return new Promise(async function streamCollectsHandler(resolve, reject) {

    //
    // 1. Get collects count
    //
    try {

      collectsCount = await getCollectsCount();

      if (isWordPressError(collectsCount)) {
        reject(collectsCount.data);

      } else {
        collectsCount = collectsCount.data.count;
      }

      if (!connectionInProgress()) {
        reject('Syncing stopped during streamCollects');
      }

    } catch(error) {

      reject(error);

    }

    //
    // 2. Get all collects
    //
    try {

      var pageSize = 250,
          currentPage = 1,
          pages = Math.ceil(collectsCount / pageSize),
          collects = [];

      // Runs for each page of collects until all done
      while(currentPage <= pages) {

        try {

          var collectsNew = await insertCollects(currentPage);

          if (!connectionInProgress()) {
            reject('Syncing stopped during streamCollects');
          }

          if (isWordPressError(collectsNew)) {
            reject(collectsNew);
          }

        } catch(errorCollects) {
          reject(errorCollects);
        }

        collects = R.concat(collects, collectsNew.data);
        currentPage += 1;

      }

      resolve(collects);

    } catch(error) {

      reject(error);

    }

  });

}


/*

Stream Smart Collections
Returns Smart Collections

*/
async function streamSmartCollections() {

  return new Promise(async function streamSmartCollectionsHandler(resolve, reject) {

    try {

      var smartCollections = await insertSmartCollections();

      if (isWordPressError(smartCollections)) {
        reject(smartCollections.data);

      } else {
        resolve(smartCollections);
      }

      if (!connectionInProgress()) {
        reject('Syncing stopped during streamSmartCollections');
      }

    } catch(error) {
      reject(error);

    }

  });

}


/*

Stream Custom Collections
Returns Smart Collections

*/
async function streamCustomCollections() {

  return new Promise(async function streamCustomCollectionsHandler(resolve, reject) {

    try {

      var customCollections = await insertCustomCollections();

      if (isWordPressError(customCollections)) {
        reject(customCollections.data);

      } else {
        resolve(customCollections);
      }

      if (!connectionInProgress()) {
        reject('Syncing stopped during streamCustomCollections');
      }

    } catch(error) {

      reject(error);

    }

  });

}


export {
  streamConnection,
  streamShop,
  streamProducts,
  streamCollects,
  streamSmartCollections,
  streamCustomCollections
}

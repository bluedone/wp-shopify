import { getErrorContents } from '../utils/utils-notices';


/*

Get Single Product by Storfront ID
Returns: Promise

*/
function getProductByID(client, productStorefrontID) {
  return client.product.fetch(productStorefrontID);
}


/*

Get Single Product by handle
Returns: Promise

*/
function getProductByHandle(client, handle) {
  return client.product.fetchByHandle(handle);
}




function getProductIDByHandle(handle) {
  return localStorage.getItem('wps-product-' + handle);
}


function setProductIDByHandle(handle, productID) {
  localStorage.setItem('wps-product-' + handle, productID);
}




/*

Adds a new line item
Returns: Promise

client, checkoutId, lineItemsToAdd

*/
function addLineItems(client, checkoutId, lineItemsToAdd) {
  return client.checkout.addLineItems(checkoutId, lineItemsToAdd);
}


/*

Get Product Variant ID

*/
function getProductVariantID(product, productVariantID) {

  return product.variants.filter(function productVariantsFilter(variant) {
    return variant.id == productVariantID;
  })[0];

}


/*

Check if any cart items are in local storage

*/
function getCheckoutID() {
  return localStorage.getItem('wps-last-checkout-id');
}




/*

Check if any cart items are in local storage

*/
function setMoneyFormatCache(moneyFormat) {

  localStorage.setItem('wps-money-format', moneyFormat);
  setCacheTime();

}


/*

Check if any cart items are in local storage

*/
function setCacheTime() {
  localStorage.setItem('wps-cache-expiration', new Date().getTime());
}


/*

Check if any cart items are in local storage

*/
function getCacheTime() {
  return localStorage.getItem('wps-cache-expiration');
}


/*

Set Product Selection ID

*/
function setProductSelectionID(id) {
  return localStorage.setItem('wps-product-selection-id', id);
}


/*

Get Product Selection ID

*/
function getProductSelectionID() {
  return localStorage.getItem('wps-product-selection-id');
}


/*

Get Product Option IDs

*/
function getProductOptionIds() {
  return localStorage.getItem('wps-option-ids');
}


/*

Set Product Option IDs

*/
function setProductOptionIds(optionIds) {
  localStorage.setItem('wps-option-ids', optionIds);
}


/*

Remove Product Option IDs

*/
function removeProductOptionIds() {
  localStorage.removeItem('wps-option-ids');
}


function getCurrentlySelectedVariants() {
  return localStorage.getItem('wps-currently-selected-variant');
}

function setCurrentlySelectedVariants(selectedVariants) {
  localStorage.setItem('wps-currently-selected-variant', JSON.stringify(selectedVariants));
}

function cacheInitialPricing() {

  var existingFromPricing = localStorage.getItem('wps-from-pricing');

  if (!existingFromPricing) {
    localStorage.setItem('wps-from-pricing', jQuery('.wps-products-price').html().trim());
  }

}

function getInitialPricing(handle) {
  return localStorage.getItem('wps-initial-pricing-' + handle);
}

function setIntialPricing(handle, html) {
  return localStorage.setItem('wps-initial-pricing-' + handle, html);
}

export {
  getProductByID,
  getProductVariantID,
  getCheckoutID,
  setMoneyFormatCache,
  setCacheTime,
  getCacheTime,
  getProductSelectionID,
  setProductSelectionID,
  getProductOptionIds,
  setProductOptionIds,
  removeProductOptionIds,
  getCurrentlySelectedVariants,
  setCurrentlySelectedVariants,
  getInitialPricing,
  setIntialPricing,
  cacheInitialPricing,
  getProductByHandle,
  getProductIDByHandle,
  setProductIDByHandle,
  addLineItems
}

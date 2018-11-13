import to from 'await-to-js';
import isArray from 'lodash/isArray';
import has from 'lodash/has';

import { needsCacheFlush, flushCache, emptyCartID } from '../utils/utils-cart';
import { clientActive, getClient } from '../utils/utils-client';
import { getCheckoutID } from './ws-products';
import { setCheckoutCache } from './ws-settings';
import { createLineItemMarkup } from '../cart/cart-ui';



/*

Checks if the checkout was completed by the user. Used to create a fresh checkout if needed.

*/
function checkoutCompleted(checkout) {

  if (has(checkout, 'completedAt') && checkout.completedAt) {
    return true;

  } else {
    return  false;
  }

}


/*

Renders all line items to cart DOM

checkout.lineItems is an array of line items

.map() creates a new array, does not mutate lineItems -- order is preserved

*/
function createLineItemsMarkup(checkout) {
  return checkout.lineItems.map( lineItem => createLineItemMarkup(lineItem) );
}


function getCheckoutByID(client, existingCheckoutID) {
  return client.checkout.fetch(existingCheckoutID);
}


function updateLineItems(client, checkout, lineItemsToUpdate) {
  return client.checkout.updateLineItems(checkout.id, lineItemsToUpdate);
}

function removeAllLineItems(client, checkout) {
  return client.checkout.removeLineItems(checkout.id, checkout.lineItems);
}

function addCheckoutAttributes(client, checkout, data) {
  return client.checkout.updateAttributes(checkout.id, { customAttributes: data });
}

/*

Fetch Cart
Returns: Promise

*/
function getCheckout(client) {

  // Calls LS
  var existingCheckoutID = getCheckoutID();

  if ( !emptyCartID(existingCheckoutID) ) {
    return getCheckoutByID(client, existingCheckoutID);
  }

  return createCheckout(client);

}


/*

Create Cart
Returns: Promise

*/
async function createCheckout(client) {
  return client.checkout.create();
}


/*

Set cart items

*/
function setCheckout(checkout) {

  if (isArray(checkout)) {
    var checkoutID = checkout[0].id;

  } else {
    var checkoutID = checkout.id;

  }

  localStorage.setItem('wps-last-checkout-id', checkoutID);

}


/*

Create Line Items From Variants

*/
function createLineItemsFromVariants(options, client) {

  return new Promise(async function(resolve, reject) {

    try {

      var checkoutID = await getCheckoutID(client);

    } catch(error) {
      reject(error);
    }

    try {

      var newCart = await client.checkout.addLineItems(checkoutID, options);
      resolve(newCart);

    } catch(error) {
      reject(error);
    }

  });

}


function cartTermsState() {

  var cartTermsState = localStorage.getItem('wps-cart-terms-accepted');

  if (cartTermsState === 'true') {
    return true;
  }

  return false;
}


function setCartTermsState(state) {
  localStorage.setItem('wps-cart-terms-accepted', state);
}


export {
  getCheckout,
  createCheckout,
  setCheckout,
  createLineItemsFromVariants,
  getCheckoutID,
  createLineItemsMarkup,
  updateLineItems,
  removeAllLineItems,
  checkoutCompleted,
  addCheckoutAttributes,
  cartTermsState,
  setCartTermsState
}

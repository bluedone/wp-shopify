/*

Get Connection Progress

*/
function connectionInProgress() {
  return localStorage.getItem('wps-connection-in-progress');
}


/*

Set Connection Progress

*/
function setConnectionProgress(status) {
  localStorage.setItem('wps-connection-in-progress', status);
}


/*

Remove Connection Nonce

*/
function removeConnectionNonce() {
  localStorage.removeItem('wps-nonce');
}


/*

Remove Cache Expiration

*/
function removeCacheExpiration() {
  localStorage.removeItem('wps-cache-expiration');
}


/*

Remove Modal Cache

*/
function removeConnectionProgress() {
  localStorage.removeItem('wps-connection-in-progress');
}


/*

Set cache modal

*/
function setModalCache(modal) {
  localStorage.setItem('wps-modal-connector-cache', modal);
}


/*

Get cache modal

*/
function getModalCache() {
  return localStorage.getItem("wps-modal-connector-cache");
}


/*

Remove Modal Cache

*/
function removeModalCache() {
  localStorage.removeItem('wps-modal-connector-cache');
}


/*

Remove Animating

*/
function removeAnimating() {
  localStorage.removeItem('wps-animating');
}


/*

Remove Last Cart ID

*/
function removeLastCartID() {
  localStorage.removeItem('wps-last-cart-id');
}


/*

Remove Product Selection

*/
function removeProductSelection() {
  localStorage.removeItem('wps-product-selection-id');
}


/*

Remove Money Format

*/
function removeMoneyFormat() {
  localStorage.removeItem('wps-money-format');
}


/*

Remove Modal Cache

*/
function clearLocalstorageCache() {
  removeModalCache();
  removeConnectionProgress();
  removeConnectionNonce();
  removeCacheExpiration();
  removeAnimating();
  removeLastCartID();
  removeProductSelection();
  removeMoneyFormat();
}


export {
  connectionInProgress,
  setConnectionProgress,
  removeConnectionProgress,
  removeConnectionNonce,
  setModalCache,
  removeModalCache,
  getModalCache,
  clearLocalstorageCache
};

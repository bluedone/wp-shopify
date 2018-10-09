import {
  findStatusCodeFirstNum
} from '../utils/utils';


/*

Get message error network

*/
function messageErrorNetwork() {
  return '504 Error: The syncing process timed out or exceeded its allocated memory. Please check <a href="https://wpshop.io/docs/syncing-errors">our documentation</a> for a potential solution.';
}


/*

4xx level errors

*/
function messageErrorClient() {
  return '400 Error: The request sent to Shopify was either malformed or corrupt. Please check <a href="https://wpshop.io/docs/syncing-errors">our documentation</a> for a potential solution.';
}


/*

Successfully updated WP Shopify settings

*/
function messageSettingsSuccessfulSave() {
  return 'Successfully updated WP Shopify settings';
}


/*

Get message error

*/
function getMessageError(error) {

  switch ( findStatusCodeFirstNum(error.status) ) {

    case 5:

      return messageErrorNetwork();
      break;

    case 4:

      return messageErrorClient();
      break;

    default:

      return messageErrorNetwork();
      break;

  }

}


export {
  getMessageError,
  messageSettingsSuccessfulSave
}

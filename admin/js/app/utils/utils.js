import forEach from 'lodash/forEach';
import isError from 'lodash/isError';
import isObject from 'lodash/isObject';
import isArray from 'lodash/isArray';
import isEmpty from 'lodash/isEmpty';
import isString from 'lodash/isString';
import has from 'lodash/has';
import isURL from 'validator/lib/isURL';
import dateFormat from 'dateformat';
import { stopSpinner } from './utils-dom';


/*

Is WordPress Error
Returns true only for wp_send_json_error

TRUE  - send_error
FALSE - send_warning
FALSE - send_success

*/
function isWordPressError(response) {

  var foundError = false;

  // A single error is being checked
  if (isObject(response) && has(response, 'success')) {

    if (!response.success) {
      foundError = true;
    }

  }

  // Used when using promise all for checking more than one returned response
  if (isArray(response) && !isEmpty(response)) {

    forEach(response, function(possibleError) {

      if (isObject(possibleError) && has(possibleError, 'success')) {

        if (!possibleError.success) {
          foundError = true;
        }

      }

    });

  }

  return foundError;

}


/*

Checks if object is one of our standard JS errors

*/
function isJavascriptError(response) {

  if (isObject(response) && has(response, 'statusCode') && has(response, 'message')) {
    return true;

  } else {
    return false;

  }

}


/*

Returns the string error message produces from a WP_Error on the server

*/
function getWordPressErrorMessage(error) {

  if ( isString(error) ) {
    return error;
  }

  if (isObject(error) && has(error, 'data') && has(error.data, 'message')) {
    return error.data.message;

  } else if ( isObject(error) && has(error, 'message') ) {
    return error.message;

  } else if ( isObject(error) && has(error, 'data') ) {

    if (isArray(error.data)) {

      if (isString(error.data[0])) {
        return error.data[0];
      }

      if (has(error.data[0], 'message')) {
        return error.data[0].message;
      }

      return 'An unknown error occured. Please clear the plugin cache and try again.';

    } else {

      return Object.values(error.data)[0].errors.error[0];

    }

  } else {
    return 'It looks like something unexpected happened. Please clear the plugin cache and try again.';

  }

}


/*

Returns the error type (error, warning, success)

*/
function getWordPressErrorType(error) {

  if (isObject(error) && has(error, 'data') && has(error.data, 'type')) {
    return error.data.type;

  } else {
    return 'error';

  }

}


/*

Returns the string error message produces from a WP_Error on the server

*/
function getJavascriptErrorMessage(error) {

  if (isError(error)) {
    return 'WP Shopify javascript error: ' + error.message;
  }

  if (isObject(error) && has(error, 'statusCode') && has(error, 'message')) {

    var died_at = has(error, 'action_name') ? error.action_name : 'unknown location';

    console.error('error ', error);

    return error.statusCode + ' Error: ' + capitalizeFirstLetter(error.message) + ' while calling ' + died_at + '. Please clear the plugin transient cache and try again.';
  }

  else {
    return false;
  }

}


function capitalizeFirstLetter(string) {
  return string.toLowerCase().replace(/^\w/, c => c.toUpperCase());
}


/*

Is WordPress Error

*/
function isConnected() {
  return jQuery('.wps-status-heading .wps-status').hasClass('is-connected');
}


/*

Remove true and transform to array

*/
function removeTrueAndTransformToArray(item) {

  var myArray = [];

  if (isObject(item)) {

    for (var key in item) {

      if (item[key] === true) {
        delete item[key];

      } else {
        myArray.push(item[key]);
      }

    }

    return myArray;

  } else {
    return item;

  }

}


/*

Check if value contains https:// or http://

*/
function containsDomain(value) {

  if( value.indexOf(".myshopify.com") === -1) {
    return false;

  } else {
    return true;

  }

}


/*

Check for HTTP(S)

*/
function containsProtocol(url) {

  if (url.indexOf("http://") > -1 || url.indexOf("https://") > -1) {
    return true;

  } else {
    return false;

  }

}


/*

If URL contains a trailing forward slash

*/
function containsTrailingForwardSlash(url) {

  if (url === undefined) {
    return false;
  }

  if ( url[url.length - 1] === '/') {
    return true;
  }

  return false;

}


/*

Removes trailing forward slash

*/
function removeTrailingForwardSlash(url) {

  var newURL = url;
  newURL = newURL.slice(0, -1);

  return newURL;

}


/*

Check for HTTP(S)

*/
function containsURL(url) {

  var options = {
    protocols: ['http','https'],
    require_tld: true,
    require_protocol: true,
    require_host: true,
    require_valid_protocol: true,
    allow_underscores: false,
    host_whitelist: false,
    host_blacklist: false,
    allow_trailing_dot: false,
    allow_protocol_relative_urls: false
  };

  var validURL = isURL(url, options);

  if (validURL) {
    return true;

  } else {
    return false;

  }

}


/*

Check for additional characters after *.myshopify.com

*/
function containsPathAfterShopifyDomain(domain) {

  if (domain.indexOf("myshopify.com")) {

    var domainSplit = domain.split('myshopify.com');

    if (domainSplit.length > 1) {
      return true;
    }

  } else {
    return false;
  }

}


/*

Remove Protocol from string

*/
function cleanDomainURL(string) {

  var newString = string;

  if (newString.indexOf("http://") > -1) {
    newString = newString.replace("http://", "");
  }

  if (newString.indexOf("https://") > -1) {
    newString = newString.replace("https://", "");
  }

  if (newString.indexOf("myshopify.com/") > -1) {
    newString = newString.split('myshopify.com/');
    newString = newString[0] + 'myshopify.com';
  }

  return newString;

}


/*

Check if value is only alphanumeric

*/
function containsAlphaNumeric(value) {
  return value.match("^[a-zA-Z0-9]*$");
}


/*

Check if value exists

*/
function containsValue(value) {
  return value.length > 0;
}


/*

Util: Get URL Parameters
Returns: Object

*/
function getUrlParamByID(match) {

  return location.search.substring(1).split("&")
    .map(function (p) { return p.split("=") })
    .filter(function (p) { return p[0] == match })
    .map(function (p) { return decodeURIComponent(p[1]) })
    .pop();
}


/*

Disable

*/
function disable($element) {
  $element.prop('disabled', true).attr('disabled', true);
}


/*

Enable

*/
function enable($element) {
  $element.prop('disabled', false).attr('disabled', false);
}


/*

Util: Enable buttons
Returns: $element without disable
TODO: Combine with the above

*/
function enableButton(button) {

  if (jQuery(button).is(':disabled')) {
    jQuery(button).prop('disabled', false);
  }

}


/*

Check for a value on a single element
Returns: Boolean

*/
function hasVal($input) {

   return $input.filter(function() {
     return jQuery(this).val();
   }).length > 0;

}


/*

Checks if all inputs have values
Returns: Boolean

*/
function hasVals($inputs) {

  var $emptyInputs = $inputs.filter(function() {
    return jQuery.trim(this.value) === "";
  });

  if(!$emptyInputs.length) {
    return true;

  } else {

    return false;

  }

}


/*

Show spinner

*/
function showSpinner(button) {

  jQuery(button).parent().next().addClass('wps-is-active');
  disable(jQuery(button));

};


/*

NEW: Show loader

*/
function showLoader($button) {

  $button.next().addClass('wps-is-active');
  disable($button);

};


/*

NEW: Hide loader

*/
function hideLoader($button) {

  $button.next().removeClass('wps-is-active');
  $button.prop("disabled", false);
  enable($button);

};


/*

Util: Reset the state of any UX indicators
Returns: undefined

*/
function resetProgressIndicators() {

  forEach(jQuery('.wps-admin-wrap .wps-spinner, .wps-connector-wrapper .wps-spinner'), stopSpinner);
  forEach(jQuery('.wps-admin-wrap .wps-btn, .wps-connector-wrapper .wps-btn, #submitConnect'), enableButton);

};


/*

Creates a masked version of a particular string

*/
function createMask(origString, mask, revealLength) {

  if (!origString) {
    return;
  }

  var origStringLength = origString.length;
  var lastFour = origString.substr(origStringLength - revealLength);
  var remaining = origString.slice(0, -revealLength);
  var remainingLength = remaining.length;
  var maskedKey = new Array(remaining.length + 1).join(mask) + lastFour;

  return maskedKey;

}


/*

Creates a masked version of a particular string

*/
function formatExpireDate(dateString) {

  var timestamp = Date.parse(dateString);

  if (isNaN(timestamp) == false) {
    var date = new Date(timestamp);
    return dateFormat(date, "mmmm d, yyyy");
  }

}

function getDataFromArray(array) {

  return array.map(function(item) {
    return item.data;
  });

}


/*

Is it a timeout? Should the request be resent?

ERROR 520
https://goo.gl/H7w7RU
---------
- Connection resets (following a successful TCP handshake)
- Headers exceed Cloudflare’s header size limit (over 8kb)
- Empty response from origin
- Invalid HTTP response
- HTTP response missing response headers


504 GATEWAY TIMEOUT
https://httpstatuses.com/504
-----------------------------
The server, while acting as a gateway or proxy, did not receive a timely response
from an upstream server it needed to access in order to complete the request.


CloudFlare status codes:
https://goo.gl/M1rHHL
------------------------
520 Unknown Error
521 Web Server
522 Connection Timed
523 Origin is Unreachable
524 A timeout Error
525 SSL handshake failed
526 Invalid SSL certificate
527 Railgun Listener to Origin Error
530 Origin DNS Error

TODO: Handle each error individually

*/
function isTimeout(statusCode) {

  // isTimeout if any of these codes are present ...
  if (statusCode == 404 || statusCode == 504 || statusCode == 408 || statusCode == 502 || statusCode == 520 || statusCode == 521 || statusCode == 522 || statusCode == 523 || statusCode == 524 || statusCode == 525 || statusCode == 526 || statusCode == 527 || statusCode == 530) {
    return true;

  } else {
    return false;
  }

}


/*

Finds the status code first number e.g, 5xx, 4xx, 3xx ...

*/
function findStatusCodeFirstNum(statusCode) {
  return Number( String(statusCode).charAt(0) );
}


/*

Turns parent Array wrap to Object. Like this:

Before:

[
  { products: 100 },
  { orders: 10 },
  { customers: 1 },
]

After:

{
  { products: 100 },
  { orders: 10 },
  { customers: 1 },
}


*/
function convertArrayWrapToObject(array) {
  return Object.assign({}, ...array);
}

export {
  findStatusCodeFirstNum,
  getUrlParamByID,
  showSpinner,
  resetProgressIndicators,
  hasVal,
  hasVals,
  disable,
  enable,
  containsDomain,
  containsAlphaNumeric,
  containsValue,
  containsProtocol,
  containsURL,
  createMask,
  formatExpireDate,
  showLoader,
  hideLoader,
  cleanDomainURL,
  containsPathAfterShopifyDomain,
  containsTrailingForwardSlash,
  removeTrailingForwardSlash,
  removeTrueAndTransformToArray,
  isWordPressError,
  isObject,
  getDataFromArray,
  isConnected,
  isTimeout,
  convertArrayWrapToObject,
  getWordPressErrorMessage,
  getWordPressErrorType,
  getJavascriptErrorMessage,
  isJavascriptError
};

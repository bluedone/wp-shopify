import {
  post,
  deletion
} from '../ws';

import {
  endpointWebhooksCount,
  endpointWebhooks
} from './api-endpoints';


/*

Get Smart Collections

Returns: promise

*/
function getWebhooks(data = {}) {
  return post( endpointWebhooks(), data);
}


/*

Get Smart Collections Count

Returns: promise

*/
function getWebhooksCount(data = {}) {
  return post( endpointWebhooksCount(), data );
}


/*

Get Smart Collections Count

Returns: promise

*/
function deleteWebhooks(data = {}) {
  return deletion( endpointWebhooks(), data );
}


/*

Registers Webhooks

*/
function registerWebhooks(data = {}) {
  return post( endpointWebhooks(), data );
}


export {
  getWebhooksCount,
  getWebhooks,
  deleteWebhooks,
  registerWebhooks
}

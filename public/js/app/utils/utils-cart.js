import {
  getCacheFlushStatus,
  updateCacheFlushStatus
} from '../ws/ws-settings';


async function needsCacheFlush() {

  try {

    var cacheFlushStatus = await getCacheFlushStatus();

    if (cacheFlushStatus.data == 1) {
      // console.info('NEEDs cache flush');
      return true;

    } else {
      // console.info('DOESNT need cache flush');
      return false;

    }

  } catch(errors) {
    // console.log('Cache flush error');
    console.error(errors);
    return true;

  }


  /*

  If recently connected, or if not connected but something exists in cart ...

  */
  // if ( !window.wps.is_connected && localStorage.getItem('wps-last-cart-id') || window.wps.is_recently_connected) {
  //   return true;
  //
  // } else {
  //   return false;
  // }

}


async function flushCache(cart) {

  localStorage.removeItem('wps-cache-expiration');
  localStorage.removeItem('wps-animating');
  localStorage.removeItem('wps-connection-in-progress');
  localStorage.removeItem('wps-product-selection-id');


  try {
    var okok = await cart.clearLineItems();
    // console.log("okok: ", okok);

  } catch(error) {
    console.error("clearLineItems error: ", error);

  }


  // Updating cache status
  try {
    await updateCacheFlushStatus(0);

  } catch(error) {
    console.error("updateCacheStatus error: ", error);

  }


}


export {
  needsCacheFlush,
  flushCache
}

import mapKeys from 'lodash/mapKeys';
import split from 'ramda/es/split';
import filter from 'ramda/es/filter';
import isEmpty from 'ramda/es/isEmpty';
import forEachObjIndexed from 'ramda/es/forEachObjIndexed';

import {
  getProgressCount,
  startProgress,
  endProgress,
  progressSessionStart
} from '../ws/ws';

import {
  isWordPressError
} from './utils';



/*

Create Progress Loader

*/
function createProgressLoader() {
  jQuery('.wps-progress-notice:first-of-type').append('<div class="wps-loader"></div>');
}


/*

Remove Progress Loader

*/
function removeProgressLoader() {
  jQuery('.wps-progress-notice .wps-loader').remove();
}


/*

Stop Progress Loader

*/
function stopProgressLoader(timer) {
  clearInterval(timer);
}


/*

Start Progress Loader

*/
function startProgressLoader() {

}


/*

Progress Status

*/
async function progressStatus() {

  console.log("Poll");

  try {

    var status = await getProgressCount(); // wps_progress_status

    if (isWordPressError(status)) {
      return;

    } else {

      var syncStatus = status.data.is_syncing;

      if (syncStatus == 1 || syncStatus == "1" || syncStatus === true) {

        /*

        At this point we know that we're still syncing and need to update
        the DOM accordingly

        */
        console.log("status: ", status);
        updateProgressBarTotals(status.data.syncing_totals);
        updateProgressBarCurrentAmounts(status.data.syncing_current_amounts);

        setTimeout(progressStatus, 1000);

      } else {
        console.log('DONE SYNCING');
      }

    }

  } catch (error) {
    console.error("getProgressCount: ", error);
  }

}


/*

Update Progress Loader

*/
function startProgressBar(resync = false, includes = []) {

  return new Promise( async (resolve, reject) => {

    try {
      var sessionVariables = await progressSessionStart(resync, includes); // wps_progress_session_create

    } catch (error) {
      reject(error);
      return;
    }

    resolve(sessionVariables);
    return sessionVariables;

  });

}


/*

Update Progress Loader

*/
function endProgressBar() {
  endProgress();
}


/*

Not Empty Value

*/
function notEmptyValue(value) {
  return !isEmpty(value);
}


/*

Shorten Session Step Names

*/
function shortenSessionStepNames(value, key, obj) {

  var splitName = split('wps_progress_current_amount_', key);
  return filter(notEmptyValue, splitName)[0];

}


/*

Map Progress Data From Session Values

*/
function mapProgressDataFromSessionValues(session) {
  return mapKeys(session, shortenSessionStepNames);
}


/*

Create Progress Bar

*/
function createProgressBar(stepName, stepTotal) {
  console.log('================ stepTotal: ', stepTotal);
  return jQuery('<div class="wps-progress-bar-wrapper" data-wps-progress-total="' + stepTotal + '" id="wps-progress-bar-' + stepName + '"><div class="wps-progress-bar"></div></div>');
}


/*

Insert Progress Bar

*/
function insertProgressBar(stepTotal, stepName) {
  jQuery('.wps-connector-content > .wps-progress-notice').first().after(createProgressBar(stepName, stepTotal));
}


/*

Progress Bar: Update Totals

*/
function updateProgressBarTotals(stepTotals) {
  forEachObjIndexed(updateProgressBarTotal, stepTotals);
}

function updateProgressBarTotal(stepTotal, stepName) {
  jQuery('#wps-progress-bar-' + stepName).find('.wps-progress-bar').data('wps-progress-total', stepTotal);
}


/*

Progress Bar: Update Current Amounts

*/
function updateProgressBarCurrentAmounts(currentAmounts) {
  console.log("currentAmounts: ", currentAmounts);
  forEachObjIndexed(updateProgressCurrentAmount, currentAmounts);
}


/*

Get Progress Bar Percentage

*/
function getProgressBarPercentage(stepCurrentValue, stepName) {

  var $progressBarWrapper = jQuery('#wps-progress-bar-' + stepName),
      $progressBar = $progressBarWrapper.find('.wps-progress-bar'),
      maxTotal = $progressBarWrapper.data('wps-progress-total'),
      currentTotal = stepCurrentValue,
      percentage = ((currentTotal / maxTotal) * 100);

  return percentage;

}


/*

Update Progress Bar Current Amount

*/
function updateProgressCurrentAmount(stepCurrentValue, stepName) {

  var percentage = getProgressBarPercentage(stepCurrentValue, stepName);
  var $progressWrapper = jQuery('#wps-progress-bar-' + stepName);

  console.log("stepCurrentValue: ", stepCurrentValue);

  if (stepCurrentValue == $progressWrapper.data('wps-progress-total')) {
    $progressWrapper.addClass('wps-is-complete');
  }

  $progressWrapper
    .find('.wps-progress-bar').css('width', percentage + '%');

}


/*

Append Progress Bars

*/
function appendProgressBars(steps) {
  console.log("steps: ", steps);
  return forEachObjIndexed(insertProgressBar, steps.wps_syncing_totals);
}



export {
  createProgressLoader,
  removeProgressLoader,
  startProgressBar,
  endProgressBar,
  progressStatus,
  mapProgressDataFromSessionValues,
  createProgressBar,
  appendProgressBars
};

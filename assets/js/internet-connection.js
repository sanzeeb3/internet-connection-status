/*
 * Global ics_params.
 */
Offline.options = {
  // to check the connection status immediatly on page load.
  checkOnLoad: '1' === ics_params.check_on_load ? true : false,

  // to monitor AJAX requests to check connection.
  interceptRequests: '1' === ics_params.intercept_requests ? true : false,

  // to automatically retest periodically when the connection is down (set to false to disable).
  reconnect: {
    // delay time in seconds to wait before rechecking.
    initialDelay: parseInt( ics_params.initial_delay ),

    // wait time in seconds between retries.
    delay: parseInt( ics_params.delay )
  },

  // to store and attempt to remake requests which failed while the connection was down.
  requests: '1' === ics_params.requests ? true : false,

 // Should we show a snake game while the connection is down to keep the user entertained?
  game: '1' === ics_params.game ? true : false,
}
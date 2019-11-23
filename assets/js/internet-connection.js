Offline.options = {
  // to check the connection status immediatly on page load.
  checkOnLoad: false,

  // to monitor AJAX requests to check connection.
  interceptRequests: true,

  // to automatically retest periodically when the connection is down (set to false to disable).
  reconnect: {
    // delay time in seconds to wait before rechecking.
    initialDelay: 3,

    // wait time in seconds between retries.
    delay: 10
  },

  // to store and attempt to remake requests which failed while the connection was down.
  requests: true
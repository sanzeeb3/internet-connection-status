jQuery(document).ready(function( $ ){ 
	jQuery( function( $ ) {
		// Review notice.
		jQuery('body').on('click', '#internet-connection-status-review-notice .notice-dismiss', function(e) {

		    e.preventDefault();

	        jQuery("#internet-connection-status-review-notice").hide();

			var data = {
				action: 'internet_connection_status_dismiss_review_notice',
				security: ics_plugins_params.review_nonce,
				dismissed: true,
			};

			$.post( ics_plugins_params.ajax_url, data, function( response ) {
				// Success. Do nothing. Silence is golden.
	    	});
		});
	});
});
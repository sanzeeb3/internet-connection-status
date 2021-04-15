<?php
/**
 * Uninstall Internet Connection Alert!
 *
 * @since 1.4.3
 */

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete all the Internet Connection Status settings.
delete_option( 'internet_connection_status' );

// Delete the review notice option.
delete_option( 'ics_review_notice_dismissed' );
<?php
/**
 * Plugin Name: Internet Connection Status
 * Description: Automatically alert your users when they've lost internet connectivity
 * Version: 1.1.0
 * Author: Sanjeev Aryal
 * Author URI: http://www.sanjeebaryal.com.np
 * Text Domain: internet-connection-status
 *
 * @see  	   https://github.com/HubSpot/offline
 *  
 * @package    Internet Connection Status
 * @author     Sanjeev Aryal
 * @since      1.0.0
 * @license    GPL-3.0+
 */

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

const ICS_VERSION = '1.1.0';

/**
 * Enqueue necessary scripts.
 *
 * @since  1.0.0
 */
function ics_enqueue_assets() {

	wp_enqueue_script( 'offline-js', plugins_url( 'assets/js/offline.min.js', __FILE__ ), array(), ICS_VERSION, true );
	wp_enqueue_script( 'internet-connection-js', plugins_url( 'assets/js/internet-connection.js', __FILE__ ), array(), ICS_VERSION, true );
	wp_enqueue_style( 'offline-language', plugins_url( 'assets/css/offline-language-english.min.css', __FILE__ ), array(), ICS_VERSION, $media = 'all' );
	wp_enqueue_style( 'offline-theme', plugins_url( 'assets/css/offline-theme-default.css', __FILE__ ), array(), ICS_VERSION, $media = 'all' );
}

add_action( 'wp_enqueue_scripts', 'ics_enqueue_assets' );

/**
 * Add Internet Connection Status Submenu
 *
 * @since  1.1.0
 */
function ics_register_setting_menu() {
	add_options_page( 'Internet Connection Status', 'Internet Connection Status', 'manage_options', 'internet-connection-status', 'ics_settings_page' );
}

/**
 * Settings page for Internet Connection Status
 *
 * @since 1.1.0
 */
function ics_settings_page() {
	$options = get_option( 'internet_connection_status', array() );
	$theme 	 = isset( $options['theme'] ) ? $options['theme'] : 'default';
	$language 	 = isset( $options['language'] ) ? $options['language'] : 'english';

	?>
		<h2 class="wp-heading-inline"><?php esc_html_e( 'Internet Connection Status Settings', 'internet-connection-status' ); ?></h2>
		<form method="post">
			<table class="form-table">
					<tr valign="top">
						   <th scope="row"><?php echo esc_html__( 'Theme', 'internet-connection-status' ); ?></th>

							<td><select name="theme">
						   		<option value="default" <?php selected( $theme, 'default' );?> ><?php echo esc_html__( 'Default', 'internet-connection-status' );?> </option>
						   		<option value="dark" <?php selected( $theme, 'dark' );?> ><?php echo esc_html__( 'Dark', 'internet-connection-status' );?> </option>
						   		<option value="chrome" <?php selected( $theme, 'chrome' );?> ><?php echo esc_html__( 'Chrome', 'internet-connection-status' );?> </option>
						   		<option value="indicator-chrome" <?php selected( $theme, 'indicator-chrome' );?> ><?php echo esc_html__( 'Indicator Chrome', 'internet-connection-status' );?> </option>
						   </select>
						</td>
					</tr>
					<tr valign="top">
						   <th scope="row"><?php echo esc_html__( 'Language', 'internet-connection-status' ); ?></th>
						   <td><select name="language">
						   		<option value="english" <?php selected( $language, 'english' );?> ><?php echo esc_html__( 'English', 'internet-connection-status' );?> </option>
						   		<option value="spanish" <?php selected( $language, 'spanish' );?> ><?php echo esc_html__( 'Spanish', 'internet-connection-status' );?> </option>
						   		<option value="french" <?php selected( $language, 'french' );?> ><?php echo esc_html__( 'French', 'internet-connection-status' );?> </option>
						   		<option value="italian" <?php selected( $language, 'italian' );?> ><?php echo esc_html__( 'Italian', 'internet-connection-status' );?> </option>
						   </select>
						</td>
					</tr>
					<?php do_action( 'internet_connection_status' ); ?>
					<?php wp_nonce_field( 'internet_connection_status', 'internet_connection_status_nonce' ); ?>

			</table>
				<?php submit_button(); ?>
		</form>
	<?php
}

/**
 * Save Settings.
 *
 * @since 1.1.0
 */
function ics_save_settings() {

	if ( isset( $_POST['internet_connection_status_nonce'] ) ) {
		if ( ! wp_verify_nonce( $_POST['internet_connection_status_nonce'], 'internet_connection_status' )
			) {
			   print 'Nonce Failed!';
			   exit;
		} else {
			$theme = isset( $_POST['theme'] ) ? sanitize_text_field( $_POST['theme'] ) : '';
			$language = isset( $_POST['language'] ) ? sanitize_text_field( $_POST['language'] ) : '';

			update_option( 'internet_connection_status', array(
				'theme'  => $theme,
				'language' => $language
			) );

		}
	}
}

add_action( 'admin_menu', 'ics_register_setting_menu' );
add_action( 'admin_init', 'ics_save_settings' );
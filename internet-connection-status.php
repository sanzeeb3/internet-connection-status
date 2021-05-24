<?php
/**
 * Plugin Name: Internet Connection Status
 * Description: Automatically alert your users when they've lost internet connectivity
 * Version: 1.4.3
 * Author: Sanjeev Aryal
 * Author URI: http://www.sanjeebaryal.com.np
 * Text Domain: internet-connection-status
 */

/**
 * @see        https://github.com/HubSpot/offline
 * @see        https://github.com/sanzeeb3/internet-connection-status
 *
 * @package    Internet Connection Status
 * @author     Sanjeev Aryal
 * @since      1.0.0
 * @license    GPL-3.0+
 */

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

const ICS_VERSION = '1.4.3';

/**
 * Load plugin textdomain.
 *
 * @since  1.4.0
 *
 * @return void.
 */
function ics_load_textdomain() {
	load_plugin_textdomain( 'internet-connection-status', false, '/languages' );
}

add_action( 'init', 'ics_load_textdomain' );

/**
 * Enqueue necessary scripts.
 *
 * @since  1.0.0
 *
 * @since  1.1.0 Dynamically enqueue theme and language.
 */
function ics_enqueue_assets() {
	$options            = get_option( 'internet_connection_status', array() );
	$theme              = isset( $options['theme'] ) ? $options['theme'] : 'default';
	$language           = isset( $options['language'] ) ? $options['language'] : 'english';
	$check_on_load      = isset( $options['check_on_load'] ) ? $options['check_on_load'] : '';
	$intercept_requests = isset( $options['intercept_requests'] ) ? $options['intercept_requests'] : '1';
	$initial_delay      = isset( $options['initial_delay'] ) ? $options['initial_delay'] : '3';
	$delay              = isset( $options['delay'] ) ? $options['delay'] : '10';
	$requests           = isset( $options['requests'] ) ? $options['requests'] : '1';
	$game               = isset( $options['game'] ) ? $options['game'] : '';

	wp_enqueue_script( 'offline-js', plugins_url( 'assets/js/offline.js', __FILE__ ), array(), ICS_VERSION, true );
	wp_enqueue_script(
		'internet-connection-js',
		plugins_url( 'assets/js/internet-connection.js', __FILE__ ),
		array(),
		ICS_VERSION,
		true
	);
	wp_enqueue_style( 'offline-language', plugins_url( 'assets/css/language/offline-language-' . $language . '.min.css', __FILE__ ), array(), ICS_VERSION, $media = 'all' );
	wp_enqueue_style( 'offline-theme', plugins_url( 'assets/css/theme/offline-theme-' . $theme . '.css', __FILE__ ), array(), ICS_VERSION, $media = 'all' );

	if ( ! empty( $game ) ) {
		wp_enqueue_script( 'offline-js-game', plugins_url( 'assets/js/snake.js', __FILE__ ), array(), ICS_VERSION, true );
	}

	$data = array(
		'check_on_load'      => $check_on_load,
		'intercept_requests' => $intercept_requests,
		'initial_delay'      => $initial_delay,
		'delay'              => $delay,
		'requests'           => $requests,
		'game'               => $game,
	);

	wp_localize_script( 'internet-connection-js', 'ics_params', $data );
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
	$options  = get_option( 'internet_connection_status', array() );
	$theme    = isset( $options['theme'] ) ? $options['theme'] : 'default';
	$language = isset( $options['language'] ) ? $options['language'] : 'english';
	$sound    = isset( $options['sound'] ) ? $options['sound'] : '0';

	$advanced_active = isset( $_GET['section'] ) && 'advanced' === $_GET['section'] ? 'nav-tab-active' : '';
	$general_active  = empty( $advanced_active ) ? 'nav-tab-active' : '';
	$template        = '<h2 class="nav-tab-wrapper">
		<a href="' . esc_url( admin_url( 'admin.php?page=internet-connection-status' ) ) . '" class="nav-tab ' . $general_active . '">General</a>
		<a href="' . esc_url( wp_nonce_url( admin_url( 'admin.php?page=internet-connection-status&section=advanced' ), 'internet-connection-status-advanced' ) ) . '" class="nav-tab ' . $advanced_active . '">' . esc_html__( 'Advanced', 'internet-connection-status' ) . '</a>
		</h2>';
	echo $template;

	$check_on_load      = isset( $options['check_on_load'] ) ? $options['check_on_load'] : '';
	$intercept_requests = isset( $options['intercept_requests'] ) ? $options['intercept_requests'] : '1';
	$initial_delay      = isset( $options['initial_delay'] ) ? $options['initial_delay'] : '3';
	$delay              = isset( $options['delay'] ) ? $options['delay'] : '10';
	$requests           = isset( $options['requests'] ) ? $options['requests'] : '1';
	$game               = isset( $options['game'] ) ? $options['game'] : '';

	// Advanced tab.
	if ( isset( $_GET['section'] ) && 'advanced' === $_GET['section'] ) {
		check_admin_referer( 'internet-connection-status-advanced' );
		?>
				<form method="post">

					<table class="form-table">
						<tr valign="top">
							<th scope="row"><?php echo esc_html__( 'Check on load', 'internet-connection-status' ); ?></th>
								<td><input type="checkbox" value="1" name="check_on_load" <?php checked( '1', $check_on_load ); ?> />
									<i class="desc"><?php echo esc_html__( 'Check the connection status immediately on page load.', 'internet-connection-status' ); ?></i>
								</td>
						</tr>

						<tr valign="top">
							<th scope="row"><?php echo esc_html__( 'Intercept Requests', 'internet-connection-status' ); ?></th>
								<td><input type="checkbox" value="1" name="intercept_requests" <?php checked( '1', $intercept_requests ); ?> />
									<i class="desc"><?php echo esc_html__( 'Monitor AJAX requests to help decide if we have a connection.', 'internet-connection-status' ); ?></i>
								</td>
						</tr>

						<tr valign="top">
							<th scope="row"><?php echo esc_html__( 'Reconnect', 'internet-connection-status' ); ?></th>
								<td><input type="number" name="initial_delay" value="<?php echo absint( $initial_delay ); ?>" /><br/>
									<i class="desc"><?php echo esc_html__( 'Seconds should we wait before rechecking.', 'internet-connection-status' ); ?></i>
								</td>
						</tr>

						<tr valign="top">
							<th scope="row"><?php echo esc_html__( 'Delay', 'internet-connection-status' ); ?></th>
								<td><input type="number" name="delay" value="<?php echo absint( $delay ); ?>" /><br/>
									<i class="desc"><?php echo esc_html__( 'Seconds should we wait between retries.', 'internet-connection-status' ); ?></i>
								</td>
						</tr>

						<tr valign="top">
							<th scope="row"><?php echo esc_html__( 'Requests', 'internet-connection-status' ); ?></th>
								<td><input type="checkbox" value="1" name="requests" <?php checked( '1', $requests ); ?> />
									<i class="desc"><?php echo esc_html__( 'Store and attempt to remake requests which fail while the connection is down.', 'internet-connection-status' ); ?></i>
								</td>
						</tr>

						<tr valign="top">
							<th scope="row"><?php echo esc_html__( 'Game', 'internet-connection-status' ); ?></th>
								<td><input type="checkbox" value="1" name="game" <?php checked( '1', $game ); ?> />
									<i class="desc"><?php echo esc_html__( 'Snake game while the connection is down to keep the user entertained.', 'internet-connection-status' ); ?></i>
								</td>
						</tr>

						<?php do_action( 'internet_connection_status_advanced' ); ?>
						<?php wp_nonce_field( 'internet_connection_status', 'internet_connection_status_nonce' ); ?>

					</table>

					<?php submit_button(); ?>

				</form>
				<?php
				return;
	}

	?>
		<form method="post">
			<table class="form-table">
					<tr valign="top">
						   <th scope="row"><?php echo esc_html__( 'Theme', 'internet-connection-status' ); ?></th>

							<td><select name="theme">
								   <option value="default" <?php selected( $theme, 'default' ); ?> ><?php echo esc_html__( 'Default', 'internet-connection-status' ); ?> </option>
								   <option value="dark" <?php selected( $theme, 'dark' ); ?> ><?php echo esc_html__( 'Dark', 'internet-connection-status' ); ?> </option>
								   <option value="chrome" <?php selected( $theme, 'chrome' ); ?> ><?php echo esc_html__( 'Chrome', 'internet-connection-status' ); ?> </option>
								   <option value="indicator-chrome" <?php selected( $theme, 'indicator-chrome' ); ?> ><?php echo esc_html__( 'Indicator Chrome', 'internet-connection-status' ); ?> </option>
						   </select>
						</td>
					</tr>
					<tr valign="top">
						   <th scope="row"><?php echo esc_html__( 'Language', 'internet-connection-status' ); ?></th>
						   <td><select name="language">
								   <option value="english" <?php selected( $language, 'english' ); ?> ><?php echo esc_html__( 'English', 'internet-connection-status' ); ?> </option>
								   <option value="spanish" <?php selected( $language, 'spanish' ); ?> ><?php echo esc_html__( 'Spanish', 'internet-connection-status' ); ?> </option>
								   <option value="french" <?php selected( $language, 'french' ); ?> ><?php echo esc_html__( 'French', 'internet-connection-status' ); ?> </option>
								   <option value="italian" <?php selected( $language, 'italian' ); ?> ><?php echo esc_html__( 'Italian', 'internet-connection-status' ); ?> </option>
								   <option value="german" <?php selected( $language, 'german' ); ?> ><?php echo esc_html__( 'German', 'internet-connection-status' ); ?> </option>
						   </select>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php echo esc_html__( 'Sound', 'internet-connection-status' ); ?></th>
							<td><input type="checkbox" value="1" name="sound" <?php checked( '1', $sound ); ?> />
								<i class="desc"><?php echo esc_html__( 'Play a beep sound when user loses their internet connection.', 'internet-connection-status' ); ?></i>
							</td>
					</tr>

					<?php do_action( 'internet_connection_status_general' ); ?>
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

			$options  = get_option( 'internet_connection_status', array() );
			$theme    = isset( $options['theme'] ) ? $options['theme'] : 'default';
			$language = isset( $options['language'] ) ? $options['language'] : 'english';
			$sound    = isset( $options['sound'] ) ? $options['sound'] : '0';

			$check_on_load      = isset( $options['check_on_load'] ) ? $options['check_on_load'] : '0';
			$intercept_requests = isset( $options['intercept_requests'] ) ? $options['intercept_requests'] : '1';
			$initial_delay      = isset( $options['initial_delay'] ) ? $options['initial_delay'] : '3';
			$delay              = isset( $options['delay'] ) ? $options['delay'] : '10';
			$requests           = isset( $options['requests'] ) ? $options['requests'] : '1';
			$game               = isset( $options['game'] ) ? $options['game'] : '0';

			if ( isset( $_GET['section'] ) && 'advanced' === $_GET['section'] ) {

				update_option(
					'internet_connection_status',
					array(
						'theme'              => $theme,
						'language'           => $language,
						'sound'              => $sound,
						'check_on_load'      => isset( $_POST['check_on_load'] ) ? sanitize_text_field( $_POST['check_on_load'] ) : '0',
						'intercept_requests' => isset( $_POST['intercept_requests'] ) ? sanitize_text_field( $_POST['intercept_requests'] ) : '0',
						'initial_delay'      => isset( $_POST['initial_delay'] ) ? sanitize_text_field( $_POST['initial_delay'] ) : '3',
						'delay'              => isset( $_POST['delay'] ) ? sanitize_text_field( $_POST['delay'] ) : '10',
						'requests'           => isset( $_POST['requests'] ) ? sanitize_text_field( $_POST['requests'] ) : '0',
						'game'               => isset( $_POST['game'] ) ? sanitize_text_field( $_POST['game'] ) : '0',
					)
				);
			} else {
				update_option(
					'internet_connection_status',
					array(
						'theme'              => isset( $_POST['theme'] ) ? sanitize_text_field( $_POST['theme'] ) : 'default',
						'language'           => isset( $_POST['language'] ) ? sanitize_text_field( $_POST['language'] ) : 'english',
						'sound'              => isset( $_POST['sound'] ) ? sanitize_text_field( $_POST['sound'] ) : '0',
						'check_on_load'      => $check_on_load,
						'intercept_requests' => $intercept_requests,
						'initial_delay'      => $initial_delay,
						'delay'              => $delay,
						'requests'           => $requests,
						'game'               => $game,
					)
				);
			}
		}
	}
}

add_action( 'admin_menu', 'ics_register_setting_menu' );
add_action( 'admin_init', 'ics_save_settings' );

/**
 * Add audio tag on the body open to play sound conditionally when should.
 *
 * @todo :: Seek for better option if all themes do not offer wp_body_open() function.
 *
 * @since  1.4.0
 */
add_action(
	'wp_body_open',
	static function() {
		$options = get_option( 'internet_connection_status', array() );
		$sound   = isset( $options['sound'] ) ? $options['sound'] : '0';

		if ( '1' === $sound ) {
			?>
				<audio id="beep" src="<?php echo plugins_url( 'assets/beep.mp3', __FILE__ ); ?>" muted></audio>
		 	<?php
		}
	}
);


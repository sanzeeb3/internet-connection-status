<?php
/**
 * Plugin Name: Internet Connection Status
 * Description: Automatically alert your users when they've lost internet connectivity
 * Version: 1.0.0
 * Author: Sanjeev Aryal
 * Author URI: http://www.sanjeebaryal.com.np
 * Text Domain: internet-connection
 *
 * @see  	   https://github.com/HubSpot/offline
 *  
 * @package    Internet Connection Status
 * @author     Sanjeev Aryal
 * @since      1.0.0
 * @license    GPL-3.0+
 */

defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

const ICS_VERSION = '1.0.0';

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

<?php
/*
Plugin Name: Rach5 Helper
Plugin URI: http://www.vanpattenmedia.com/
Description: Helper functions for Rach5 themes
Author: Van Patten Media
Version: 0.7
Author URI: http://www.vanpattenmedia.com/
*/

/**
 * Rach5 Helper
 *
 * @version 0.7
 * @package rach5-helper
 */


/*
 *
 * Set up constants and base functions
 *
 */

define( 'RACH5_HELPER_PATH', plugin_dir_path( __FILE__ ) );


/**
 * function wp_base_dir
 * returns WordPress subdirectory if applicable
 * @since 0.1
 */
function wp_base_dir() {
	preg_match('!(https?://[^/|"]+)([^"]+)?!', site_url(), $matches);
	if (count($matches) === 3) {
		return end($matches);
	} else {
		return '';
	}
}

/**
 * function rach5_info
 * @since 0.1
 */
function rach5_info() {
	if ( !defined('__DIR__') )
		define('__DIR__', dirname(__FILE__));

	$exploded   = explode( '/themes/', get_template_directory() );
	$theme_name = next( $exploded );

	// Get some info about the site
	define('WP_BASE', wp_base_dir());
	define('THEME_NAME', $theme_name);
	define('RELATIVE_PLUGIN_PATH', str_replace(site_url() . '/', '', plugins_url()));
	define('FULL_RELATIVE_PLUGIN_PATH', WP_BASE . '/' . RELATIVE_PLUGIN_PATH);
	define('RELATIVE_CONTENT_PATH', str_replace(site_url() . '/', '', content_url()));
	define('THEME_PATH', RELATIVE_CONTENT_PATH . '/themes/' . THEME_NAME);
}
rach5_info();


/*
 *
 * Require everything else
 *
 */

require_once( RACH5_HELPER_PATH . 'functions/functions.php');
require_once( RACH5_HELPER_PATH . 'functions/clean.php');
require_once( RACH5_HELPER_PATH . 'functions/html5.php');
require_once( RACH5_HELPER_PATH . 'functions/admin.php');

/**
 * Enable default rach5 features
 */
$rach5_theme_features = array(
	"htaccess" => true,
	"root-relative-urls" => true,
);


/**
 * function add_rach5_support
 * Explicitly enable features
 * @since 0.3
 */

function add_rach5_support( $feature ) {
	global $rach5_theme_features;

	if ( func_num_args() == 1 ) {
		$rach5_theme_features[$feature] = true;
	} else {
		$rach5_theme_features[$feature] = array_slice( func_get_args(), 1 );
	}
}


/**
 * function remove_rach5_support
 * Explicitly disable rach5 features
 * @since 0.3
 */

function remove_rach5_support( $feature ) {
	global $rach5_theme_features;

	if ( in_array( $feature, $rach5_theme_features ) ) {
		unset( $rach5_theme_features[$feature] );
		return true;
	}

	if ( ! isset( $rach5_theme_features[$feature] ) ) {
		return false;
	}
}


/**
 * function do_rach5_support
 * Set up Rach5 support
 * @since 0.3
 */

function do_rach5_support() {
	global $rach5_theme_features;

	if ( array_key_exists('htaccess', $rach5_theme_features) ) {
		require_once( RACH5_HELPER_PATH . 'functions/htaccess.php');
	}

	if ( array_key_exists('root-relative-urls', $rach5_theme_features) ) {
		require_once( RACH5_HELPER_PATH . 'functions/root-relative-urls.php');
	}

	if ( array_key_exists('wrappers', $rach5_theme_features) ) {
		require_once( RACH5_HELPER_PATH . 'functions/wrappers.php');
	}
}
add_action('init', 'do_rach5_support');

<?php
/*
Plugin Name: Rach5 Helper
Plugin URI: http://www.vanpattenmedia.com/
Description: Helper functions for Rach5 themes
Author: Van Patten Media
Version: 0.1
Author URI: http://www.vanpattenmedia.com/
*/

/**
 *
 * Set up constants and base functions
 *
 */

define( 'RACH5_HELPER_PATH', plugin_dir_path( __FILE__ ) );

// returns WordPress subdirectory if applicable
function wp_base_dir() {
	preg_match('!(https?://[^/|"]+)([^"]+)?!', site_url(), $matches);
	if (count($matches) === 3) {
		return end($matches);
	} else {
		return '';
	}
}

function rach5_info() {
	if (!defined('__DIR__')) { define('__DIR__', dirname(__FILE__)); }
	
	// Get some info about the site
	define('WP_BASE', wp_base_dir());
	define('THEME_NAME', next(explode('/themes/', get_template_directory())));
	define('RELATIVE_PLUGIN_PATH', str_replace(site_url() . '/', '', plugins_url()));
	define('FULL_RELATIVE_PLUGIN_PATH', WP_BASE . '/' . RELATIVE_PLUGIN_PATH);
	define('RELATIVE_CONTENT_PATH', str_replace(site_url() . '/', '', content_url()));
	define('THEME_PATH', RELATIVE_CONTENT_PATH . '/themes/' . THEME_NAME);
}
rach5_info();

/**
 *
 * Require everything else
 *
 */
 
require_once( RACH5_HELPER_PATH . 'functions/functions.php');
require_once( RACH5_HELPER_PATH . 'functions/admin.php');
require_once( RACH5_HELPER_PATH . 'functions/clean.php');
require_once( RACH5_HELPER_PATH . 'functions/htaccess.php');
require_once( RACH5_HELPER_PATH . 'functions/html5.php');


/**
 *
 * Plugin Updater
 *
 */
 
require_once( RACH5_HELPER_PATH . 'updater.php');
<?php

/**
 *
 * Van Patten Media Plugin Updater
 *
 * Version 1.0
 * 10 April 2012
 *
 */

// Plugin prefix = rach5helper (find and replace me)
$rach5helper_api_url = 'https://updates.vanpattenmedia.com/';
$rach5helper_plugin_slug = basename(dirname(__FILE__));


/**
 *
 * Force-update the plugin, with a forced 60 second window between checks.
 *
 * This should be standard in WordPress 3.4, see Trac #18876
 *
 */

if ( !function_exists('reset_plugin_transient') ) {
	function reset_plugin_transient() {
		$file = basename( $_SERVER['PHP_SELF'] );
		if ( $file == ('update-core.php' || 'plugins.php') ) {
			$current = get_site_transient( 'update_plugins' );
			$difference = time() - $current->last_checked;
			if ( isset( $current->last_checked ) && ( $difference >= 60 ) ) {
				$current->last_checked = time();
				set_site_transient('update_plugins', $current);
			}
		}
	}
	add_action('admin_head', 'reset_plugin_transient');
}


/**
 *
 * Set up the request.
 *
 */

if ( !function_exists('vpm_update_request') ) {
	function vpm_update_request($action, $args) {
		global $wp_version;

		return array(
			'body' => array(
				'action' => $action,
				'request' => serialize($args),
				'api-key' => md5(get_bloginfo('url'))
			),
			'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
		);
	}
}


/**
 *
 * Now that we've reset the plugin updater, we can hijack it.
 *
 */

function rach5helper_update_check($checked_data) {
	global $rach5helper_api_url, $rach5helper_plugin_slug;

	if (empty($checked_data->checked)) {
		return $checked_data;
	}

	// Set up the request string
	$request_args = array(
		'slug' => $rach5helper_plugin_slug,
		'version' => $checked_data->checked[$rach5helper_plugin_slug .'/'. $rach5helper_plugin_slug .'.php']
	);

	$request_string = vpm_update_request('basic_check', $request_args);

	// Start checking for an update
	$raw_response = wp_remote_post($rach5helper_api_url, $request_string);

	if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200)) {
		$response = unserialize($raw_response['body']);
	}

	// Feed the update data into WP updater
	if (is_object($response) && !empty($response)) {
		$checked_data->response[$rach5helper_plugin_slug .'/'. $rach5helper_plugin_slug .'.php'] = $response;

		return $checked_data;
	}
}
add_filter('pre_set_site_transient_update_plugins', 'rach5helper_update_check');


/**
 *
 * Display the plugin information through the API.
 *
 */

function rach5helper_api_call($def, $action, $args) {
	global $rach5helper_plugin_slug, $rach5helper_api_url;

	if ($args->slug != $rach5helper_plugin_slug)
		return false;

	// Get the current version
	$plugin_info = get_site_transient('update_plugins');
	$current_version = $plugin_info->checked[$rach5helper_plugin_slug .'/'. $rach5helper_plugin_slug .'.php'];
	$args->version = $current_version;

	$request_string = vpm_update_request($action, $args);

	$request = wp_remote_post($rach5helper_api_url, $request_string);

	if (is_wp_error($request)) {
		$res = new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>'), $request->get_error_message());
	} else {
		$res = unserialize($request['body']);

		if ($res === false) {
			$res = new WP_Error('plugins_api_failed', __('An unknown error occurred'), $request['body']);
		}
	}

	return $res;
}
add_filter('plugins_api', 'rach5helper_api_call', 10, 3);
<?php

/**
 * Rach5 Helper
 * Root relative URLs
 *
 * @version 0.3
 * @package rach5-helper
 */


 /**
 * function rach5_root_relative_url
 * Root relative URLs
 * @link http://bit.ly/a35LmX
 * @since 0.1
 */

function rach5_root_relative_url($input) {
	$output = preg_replace_callback(
		'!(https?://[^/|"]+)([^"]+)?!',
		create_function(
			'$matches',
			// if full URL is site_url, return a slash for relative root
			'if (isset($matches[0]) && $matches[0] === site_url()) { return "/";' .
			// if domain is equal to site_url, then make URL relative
			'} elseif (isset($matches[0]) && strpos($matches[0], site_url()) !== false) { return $matches[2];' .
			// if domain is not equal to site_url, do not make external link relative
			'} else { return $matches[0]; };'
		),
		$input
	);
	return $output;
}


/**
 * function rach5_fix_duplicate_subfolder_urls
 * Clean up duplicate subfolders
 * @since 0.2
 */

function rach5_fix_duplicate_subfolder_urls($input) {
	$output = rach5_root_relative_url($input);
	preg_match_all('!([^/]+)/([^/]+)!', $output, $matches);
	if (isset($matches[1]) && isset($matches[2])) {
		if ($matches[1][0] === $matches[2][0]) {
			$output = substr($output, strlen($matches[1][0]) + 1);
		}
	}
	return $output;
}

if (!is_admin() && !in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'))) {
	$tags = array(
		'bloginfo_url',
		'theme_root_uri',
		'stylesheet_directory_uri',
		'template_directory_uri',
		'script_loader_src',
		'style_loader_src',
		'plugins_url',
		'the_permalink',
		'wp_list_pages',
		'wp_list_categories',
		'wp_nav_menu',
		'the_content_more_link',
		'the_tags',
		'get_pagenum_link',
		'get_comment_link',
		'month_link',
		'day_link',
		'year_link',
		'tag_link',
		'the_author_posts_link'
	);
	add_filters($tags, 'rach5_root_relative_url');
	add_filter('script_loader_src', 'rach5_fix_duplicate_subfolder_urls');
	add_filter('style_loader_src', 'rach5_fix_duplicate_subfolder_urls');
}


/**
 * function rach5_root_relative_attachment_urls
 * Clean up attachment URLs
 * @since 0.1
 */

function rach5_root_relative_attachment_urls() {
	if (!is_feed()) {
		add_filter('wp_get_attachment_url', 'rach5_root_relative_url');
		add_filter('wp_get_attachment_link', 'rach5_root_relative_url');
	}
}
add_action('pre_get_posts', 'rach5_root_relative_attachment_urls');
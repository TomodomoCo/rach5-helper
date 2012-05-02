<?php

/**
 * Rach5 Helper
 * Clean the source code
 *
 * @version 0.3
 * @package rach5-helper
 */


/**
 * function rach5_title_despacer
 * Remove spaces from wp_title without defined separator
 * @since 0.1
 */

function rach5_title_despacer($title) {
	return trim($title);
}
add_filter('wp_title', 'rach5_title_despacer');


/**
 * function strip_page_from_body_class
 * Only display "home" class on home when set to use page as homepage
 * @since 0.1
 */

function strip_page_from_body_class($classes, $class) {
	global $post;
	if ( !is_front_page() ){
		return $classes;
	} else {
		foreach ($classes as &$str) {
			if (strpos($str, "page") > -1) {
				$str = "";
			}
		}
	}
	return $classes;
}
add_filter("body_class", "strip_page_from_body_class", 10, 2);


/**
 * function rach5_disable_version
 * Remove WordPress version from RSS feeds
 * @since 0.1
 */

function rach5_disable_version() {
	return '';
}
add_filter('the_generator','rach5_disable_version');


/**
 * function rach5_rel_canonical
 * Better canonical links
 * @since 0.1
 */

function rach5_rel_canonical() {
	if (!is_singular()) {
		return;
	}

	global $wp_the_query;
	if (!$id = $wp_the_query->get_queried_object_id()) {
		return;
	}

	$link = get_permalink($id);
	echo "<link rel=\"canonical\" href=\"$link\">\n";
}


/**
 * function rach5_head_cleanup
 * Remove unnecessary bits of the head and inject others
 * @since 0.1
 */

function rach5_head_cleanup() {
	remove_action('wp_head', 'wlwmanifest_link');
	remove_action('wp_head', 'rsd_link');
	remove_action('wp_head', 'wp_generator');
	remove_action('wp_head', 'feed_links_extra', 3);
	remove_action('wp_head', 'index_rel_link');
	remove_action('wp_head', 'parent_post_rel_link', 10, 0);
	remove_action('wp_head', 'start_post_rel_link', 10, 0);
	remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
	remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
	remove_action('wp_head', 'noindex', 1);
	// remove_action('wp_head', 'feed_links', 2);

	remove_action('wp_head', 'rel_canonical');
	add_action('wp_head', 'rach5_rel_canonical');

	if (!is_admin()) {
		wp_deregister_script('l10n');
		wp_deregister_script('jquery');
		wp_register_script('jquery', '', '', '', true);
	}
}
add_action('init', 'rach5_head_cleanup');
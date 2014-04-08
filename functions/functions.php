<?php

/**
 * Rach5 Helper
 * Master functions
 *
 * @version 0.7
 * @package rach5-helper
 */


/**
 * function stylesheet_link_tag
 * Borrowed from Roots, inspired by Rails
 * Options:
 *   $file    = Location of file
 *   $local   = Local or remote file? (e.g. include get_template_directory_uri() or not)
 *   $tabs    = Number of tabs to proceed the line
 *   $newline = Add a newline after?
 *   $rel     = "stylesheet" by default, but you can change it if you want
 * @since 0.1
 * @deprecated Deprecated since 0.3
 */

function stylesheet_link_tag($file, $local = true, $tabs = 0, $newline = true, $rel = 'stylesheet') {
	$indent = str_repeat("\t", $tabs);
	echo $indent . '<link rel="' . $rel .'" href="' . ($local ? get_template_directory_uri() . '/css' : '') . $file . '">' . ($newline ? "\n" : "");
}


/**
 * function leadingslashit
 * opposite of built in WP functions for trailing slashes
 * @since 0.1
 */

function leadingslashit($string) {
  return '/' . unleadingslashit($string);
}


/**
 * function unleadingslashit
 * opposite of built in WP functions for trailing slashes
 * @since 0.1
 */

function unleadingslashit($string) {
  return ltrim($string, '/');
}


/**
 * function add_filters
 * Run multiple filters more easily
 * @since 0.1
 */

function add_filters($tags, $function) {
  foreach($tags as $tag) {
    add_filter($tag, $function);
  }
}


/**
 * function copyright
 * What year should the copyright start?
 * @since 0.1
 */

function copyright($copystart) {
	echo 'Copyright &copy; ' . $copystart;

	if ( date('Y') > $copystart ) {
		echo '-' . date('Y');
	}
}


/**
 * function tab
 * A nice tab function, if you like clean source like me.
 * @deprecated Deprecated since version 0.2
 */

function tab($count=1){
    for($x = 1; $x <= $count; $x++){
        $output .= "\t";
    }
    return $output;
}


/**
 * function new_excerpt_more
 * Clean up the end of excerpts.
 * @since 0.3
 */

function new_excerpt_more($more) {
	return '...';
}
add_filter('excerpt_more', 'new_excerpt_more');


/**
 * function rach5_get_the_excerpt
 * Custom get_the_excerpt function
 * @since 0.1
 */

function rach5_get_the_excerpt( $count = 20 ) {
	global $posts;

	if ( empty($posts[0]->post_excerpt) ) {
		// 1. Get the initial data for the excerpt
		$content = $posts[0]->post_content;

		// 2. Strip tags from $content
		$stripped_content = strip_tags($content);
		
		// 3. Trim words from $content, at the set count length
		$trimmed_content = wp_trim_words($stripped_content, $count);

		// 4. Here's your excerpt!
		$rach5_excerpt = str_replace("\n", ' ', $trimmed_content);
	} else {
		// When the post excerpt has been set explicitly, then it has priority.
		$rach5_excerpt = $posts[0]->post_excerpt;
	}

	return $rach5_excerpt;
}


/**
 * rach5_options
 * Get the ID of the options page
 * @since 0.7
 */

function rach5_options() {
	$options = get_page_by_path('options');
	return $options->ID;
}


/**
 * rach5_image
 * Get the URL of an image from its ID
 * @since 0.7
 */

function rach5_image( $id, $size = 'full' ) {
	$image = wp_get_attachment_image_src( $id, $size );
	return $image[0];
}


/**
 * rach5_is_child_of
 * Conditional function to detect if a page is a sub-page
 * @since 0.7
 */

function rach5_is_child_of( $slug ) {
	$child = get_page_by_path( $slug );

	global $post;
	if ( $post->post_parent == $child->ID ) {
		return true;
	} else {
		return false;
	}
}

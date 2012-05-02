<?php

/**
 * Rach5 Helper
 * Scribu theme wrappers
 *
 * @version 0.3
 * @package rach5-helper
 */

/**
 * function rach5_template_path
 * @since 0.3
 */
function rach5_template_path() {
	return Rach5_Wrapping::$main_template;
}

/**
 * function rach5_template_base
 * @since 0.3
 */
function rach5_template_base() {
	return Rach5_Wrapping::$base;
}

/**
 * class Rach5_Wrapping
 * @since 0.3
 */
class Rach5_Wrapping {

	/**
	 * Stores the full path to the main template file
	 */
	static $main_template;

	/**
	 * Stores the base name of the template file; e.g. 'page' for 'page.php' etc.
	 */
	static $base;

	/**
	 * function wrap
	 * @since 0.3
	 */
	static function wrap( $template ) {
		self::$main_template = $template;

		self::$base = substr( basename( self::$main_template ), 0, -4 );

		if ( 'index' == self::$base )
			self::$base = false;

		$templates = array( 'base.php' );

		if ( self::$base )
			array_unshift( $templates, sprintf( 'base-%s.php', self::$base ) );

		return locate_template( $templates );
	}
}
add_filter( 'template_include', array( 'Rach5_Wrapping', 'wrap' ), 99 );
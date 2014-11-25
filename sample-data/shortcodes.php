<?php
/**
 * Shortcodes definitions
 * 
 * @package skelet
 */

// Make sure our temporary variable is empty
$shortcodes = array();

$shortcodes[ 'my_shortcode_keyboard' ] = array(
	'title'     => __( 'Keyboard' ),
	'function'  => 'raw', // raw, wrap, param
	'image'     => 'http://findicons.com/files/icons/85/kids/32/keyboard.png',
);

$shortcodes[ 'my_shortcode_cookie' ] = array(
	'title'     => __( 'Cookie' ),
	'function'  => 'raw', // raw, wrap, param
	'image'     => 'http://findicons.com/files/icons/85/kids/32/cookie.png',
);

$shortcodes[ 'my_shortcode_text' ] = array(
	'title'     => __( 'Text' ),
	'function'  => 'raw', // raw, wrap, param
	'text'      => 'Text title', // raw, wrap, param
);

// Register options
paf_shortcodes( $shortcodes );

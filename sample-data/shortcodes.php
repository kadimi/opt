<?php
/**
 * Shortcodes definitions
 * 
 * @package skelet
 */

// Make sure our temporary variable is empty
$shortcodes = array();

$shortcodes[ 'my_shortcode' ] = array(
	'post_type' => 'post',
	'title'     => __( 'My Shortcode' ),
	'type'      => 'raw', // raw, wrap, param
);

// Register options
paf_shortcodes( $shortcodes );

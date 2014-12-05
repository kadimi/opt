<?php
/**
 * Shortcodes definitions
 * 
 * @package skelet
 */

// Make sure our temporary variable is empty
$shortcodes = array();

/**
 * $dir in this example translates to where this file resides relative to the site
 * domain name, ex: "/wp/wp-content/plugins/some_plugin/skelet". Use it to set the
 * icon paths, so if your icon is in "/wp/wp-content/plugins/some_plugin/skelet/dummy.png"
 * set "image" to "$dir/dummy.png".
 */
$dir = parse_url( site_url(), PHP_URL_PATH ) . str_replace( ABSPATH, '/', dirname( __FILE__ ) );

$shortcodes[ 'skelet_basic' ] = array(
	'title' => __( 'Insert text/ Replace selection' ),
	'wrap'  => false,
	'image' => "$dir/shortcode.png",
	'shortcode' => 'skelet_basic_cb'
);
function skelet_basic_funcz( $atts = array(), $content = null ) {
	return sprintf( __( 'Hello from <strong>%s</strong>.' ), func_get_arg( 2 ) );
}

$shortcodes[ 'skelet_wrap' ] = array(
	'title' => __( 'Wrap Selection' ),
	'wrap'  => true,
	'image' => 'http://findicons.com/files/icons/85/kids/32/keyboard.png',
);
function skelet_wrap_func( $atts = array(), $content = null ) {
	return sprintf( __( 'Hello from <strong>%s</strong>, I was given this content: <strong>%s</strong>.' )
		, func_get_arg( 2 )
		, strlen( $content ) ? $content : '&lt;nothing&gt;'
	);
}

$shortcodes[ 'skelet_model' ] = array(
	'title'      => __( 'With parameters (open popup)' ),
	'text'       => 'Advanced',
	'wrap'       => true,
	'height'     => .8,
	'width'      => .5,
	'parameters' => array(
		'p1' => array(
			'title'       => __( 'Title' ),
			'subtitle'    => __( 'Subtitle with <code>code</code>' ),
			'description' => __( '<i class="dashicons dashicons-editor-help"></i>Mind some help text?' ),
			'placeholder' => __( 'Oh! A placeholder...' ),
		),
		'p2' => array(
			'type'    => 'checkbox',
			'options' => 'posts',
			'args'    => array(
				'post_status' => 'publish',
				'post_type' => 'post',
			),
		),
		'p3' => array(
			'type'     => 'radio',
			'options'  => 'terms',
			'multiple' => TRUE,
		),
	),
);

// Register options
paf_shortcodes( $shortcodes );

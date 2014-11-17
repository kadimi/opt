<?php

/**
 * Shortcodes definitions
 * 
 * @package skelet
 */

// Make sure our temporary variable is empty
$shortcodes = array();

$shortcodes[ 'my_shortcode' ] = array(
	'title' => __( 'My Shortcode' ),
	'post_type' => 'post',
);

// Register options
paf_shortcodes( $shortcodes );

// add new buttons
function my_shortcode( $buttons ) {
   array_push($buttons, 'separator', 'my_shortcode');
   return $buttons;
}
add_filter('mce_buttons', 'my_shortcode');
 
// Load the TinyMCE plugin : editor_plugin.js (wp2.5)
add_filter('mce_external_plugins', 'myplugin_register_tinymce_javascript');

function myplugin_register_tinymce_javascript($plugin_array) {
	add_action( 'admin_footer', 'the_js' );
	return $plugin_array;
}

function the_js() {
	?><script>
		window.alert( "1" );
	</script><?php
}
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
// paf_shortcodes( $shortcodes );

// add new buttons
function my_shortcode( $buttons ) {
	return array_merge( $buttons, array( 'button_eek', 'button_green' ) );
}
add_filter('mce_buttons', 'my_shortcode');

// Load the TinyMCE plugin : editor_plugin.js (wp2.5)
add_filter('mce_external_plugins', 'myplugin_register_tinymce_javascript');

function myplugin_register_tinymce_javascript($plugin_array) {

	return array(
		'skelet_tinyMCE' => site_url( 'skelet_tinyMCE.js' ),
	);
}

/**
 * Add JavaScript file containing TinyMCE plugins
 */
add_action( 'init', 'skelet_endpoints' );
add_filter( 'request', 'skelet_tinyMCE_plugin' );

function skelet_endpoints() {

	add_rewrite_endpoint( 'skelet_tinyMCE.js', EP_ROOT );
}

function skelet_tinyMCE_plugin() {
	?>(function() {
		 /* Register the buttons */
		 tinymce.create('tinymce.plugins.skelet_tinyMCE', {
			  init : function(ed, url) {
					/**
					* Inserts shortcode content
					*/
					ed.addButton( 'button_eek', {
						title : 'Insert shortcode',
						image : '../wp-includes/images/smilies/icon_eek.gif',
						onclick : function() {
							 ed.selection.setContent('[myshortcode]');
						}
					});
					/**
					* Adds HTML tag to selected content
					*/
					ed.addButton( 'button_green', {
						title : 'Add span',
						image : '../wp-includes/images/smilies/icon_mrgreen.gif',
						cmd: 'button_green_cmd'
					});
					ed.addCommand( 'button_green_cmd', function() {
						var selected_text = ed.selection.getContent();
						var return_text = '';
						return_text = '<h1>' + selected_text + '</h1>';
						ed.execCommand('mceInsertContent', 0, return_text);
					});
				},
				createControl : function(n, cm) {
					return null;
				},
			});
			/* Start the buttons */
			tinymce.PluginManager.add( 'skelet_tinyMCE', tinymce.plugins.skelet_tinyMCE );
	})();<?php
	exit;
}

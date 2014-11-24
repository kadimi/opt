<?php

/**
 * @package skelet
 */

// Load the TinyMCE plugin 
add_filter( 'mce_external_plugins', 'skelet_add_tinyMCE_plugin' );
function skelet_add_tinyMCE_plugin() {
	return array( 'skelet' => site_url( 'skelet/tinyMCE.js' ) );
}

// Add endpoint(s)
add_action( 'init', 'skelet_add_endpoint' );
function skelet_add_endpoint() {
	add_rewrite_rule( '^skelet/tinyMCE.js$', 'index.php?skelet=tinyMCE_js', 'top' );
} 

// Add the query_var "skelet"
add_filter( 'query_vars', 'skelet_add_query_vars' );
function skelet_add_query_vars( $vars ) {
	$vars[] = 'skelet';
	return $vars;
}

// Capture requests and serve files 
add_action( 'parse_request', 'skelet_sniff_requests' );
function skelet_sniff_requests() {

	global $wp;
	$serve = K::get_var( 'skelet', $wp->query_vars );

	switch ( $serve ) {
	case 'tinyMCE_js':
		call_user_func( 'skelet_' . $serve  );
		exit;
	}
}

// Add buttons to WordPress editor
add_filter( 'mce_buttons', 'skelet_tinyMCE_buttons' );
function skelet_tinyMCE_buttons( $buttons ) {

	return array_merge( $buttons, array_keys( $GLOBALS[ 'paf_shortcodes' ] ) );
}

// output for "skelet/tinyMCE.js"
function skelet_tinyMCE_js() {

	global $paf_shortcodes;
	?>
	(function() {
		/* Register the buttons */
		tinymce.create('tinymce.plugins.skelet', {
			init : function(ed, url) {
			<?php foreach ( $paf_shortcodes as $tag => $options ) { ?>
				/**
				* Inserts shortcode content
				*/
				ed.addButton( '<?php echo $tag ?>', {
					title : '<?php echo $options[ 'title' ] ?>',
					image : '../wp-includes/images/smilies/icon_eek.gif',
					onclick : function() {
						 ed.selection.setContent('[<?php echo $tag ?>]');
					}
				});
			<?php } ?>
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
		tinymce.PluginManager.add( 'skelet', tinymce.plugins.skelet );
	})();
	<?php
}

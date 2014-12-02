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

	$rules = array(
		'tinyMCE\.js' => 'tinyMCE_js',
		'tinyMCE\.php/([a-zA-Z_][a-zA-Z0-9_-]*)' => 'tinyMCE_php&tag=$matches[1]',
	);
	
	foreach ($rules  as $regex => $redirect ) {
		add_rewrite_rule(
			sprintf( '^skelet/%s$', $regex )
			, sprintf( 'index.php?skelet=%s', $redirect )
			, 'top'
		);
	}
} 

// Add the query_var "skelet" and "tag"
add_filter( 'query_vars', 'skelet_add_query_vars' );
function skelet_add_query_vars( $vars ) {
	$vars[] = 'skelet';
	$vars[] = 'tag';
	return $vars;
}

// Capture requests and serve files 
add_action( 'parse_request', 'skelet_sniff_requests' );
function skelet_sniff_requests() {

	global $wp;
	$serve = K::get_var( 'skelet', $wp->query_vars );
	$tag = K::get_var( 'tag', $wp->query_vars );

	switch ( $serve ) {
	case 'tinyMCE_php':
		$shortcode = K::get_var( $tag, $GLOBALS[ 'paf_shortcodes' ] );
		// Exist if shortcode doesn't exist or doesn't have parameters
		if( ! $shortcode || ! K::get_var( 'parameters', $shortcode ) ) {
			exit;
		}
		// Show HTML for shortcode popup window
		header( 'Content-Type: text/html; charset=utf-8');
		wp_die( call_user_func( 'skelet_' . $serve, $tag ) );
	case 'tinyMCE_js':
		header( 'Content-Type: application/javascript; charset=utf-8' );
		die(
			trim(
				// Remove multiple spaces
				preg_replace( '#\s+#', ' ',
					// Remove comments like /* ... */
					preg_replace( '#\/\*([^*])*\*\/#', '',
						call_user_func( 'skelet_' . $serve  )
					)
				)
			)
		);
	}
}

// Add buttons to WordPress editor
add_filter( 'mce_buttons', 'skelet_tinyMCE_buttons' );
function skelet_tinyMCE_buttons( $buttons ) {

	return array_merge( $buttons, array_keys( $GLOBALS[ 'paf_shortcodes' ] ) );
}

// output for "skelet/tinyMCE.php/$tag"
function skelet_tinyMCE_php( $tag ) {
	global $paf_shortcodes;
	
	$select2_enqueued = false;
	$protocol = is_ssl() ? 'https' : 'http';

	ob_start();

	// CSS
	printf( '<link rel="stylesheet" href="%s" />', admin_url( 'css/forms' . ( is_rtl() ? '-rtl' : '' ) . '.css' ) );
	print( '<style>'
		. 'html { background: white; }'
		. 'body { -webkit-box-shadow: none; box-shadow: none; margin: 0 auto !important; }'
		. '</style>'
	);

	// JS
	printf( '<script src="%s"></script>', "$protocol://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.1/jquery.min.js" );

	// Fields
	$parameters = $paf_shortcodes[ $tag ]['parameters'];
	foreach ( $parameters as $k => $v ) {
		// Validate title
		$v[ 'title' ] = k::get_var( 'title', $v, $k );
		// Print option
		paf_print_option( 'dummy', $v );
		if( 'select' === $v[ 'type' ] ) {
			printf("\n\n");
			printf( '<link rel="stylesheet" href="%s" />', "$protocol://cdnjs.cloudflare.com/ajax/libs/select2/3.5.0/select2.min.css" );
			printf("\n\n");
			printf( '<script src="%s"></script>', "$protocol://cdnjs.cloudflare.com/ajax/libs/select2/3.5.0/select2.js" );
			printf("\n\n");
			print( "<script>jQuery( document ).ready( function( $ ) { $( 'select' ).select2(); } );</script>" );
			printf("\n\n");
			$select2_enqueued = true;
		}
	}

	// Remove the error class
	return ob_get_clean();
}

// output for "skelet/tinyMCE.js"
function skelet_tinyMCE_js() {
	global $paf_shortcodes;

	ob_start();
	?>
	/*<script>*/
	(function () {

		var $ = jQuery;

		/* Register the buttons */
		tinymce.create('tinymce.plugins.skelet', {
			init : function(ed, url) {
				var tag;
				var specs;

				<?php foreach ( $paf_shortcodes as $tag => $specs ) { ?>
					<?php $specs[ 'parameters' ] = k::get_var( 'parameters', $specs, false ); ?>

					<?php if( $specs[ 'parameters' ] ) { ?>
						
						tag = '<?php echo $tag ?>';
						specs = <?php echo json_encode( $specs, JSON_FORCE_OBJECT ); ?>;



						specs.onclick = function() { 
							
							var specs = <?php echo json_encode( $specs, JSON_FORCE_OBJECT ); ?>
								, tag = '<?php echo $tag ?>'
							;
							var w = $( window ).width() * .7;

							var h = $( window ).height() * .7;
							if( w > 800 ) w = 800;
							
							ed.windowManager.open( {
								title: specs.title,
								text: specs.text,
								url: '<?php echo site_url( "skelet/tinyMCE.php/$tag" ); ?>',
								width: w,
								height: h,
								maximizable: true,
								resizable: true
							} );
						};

					<?php } else { ?>
						
						tag = '<?php echo $tag ?>';
						specs = <?php echo json_encode( $specs, JSON_FORCE_OBJECT ); ?>;
						specs.onclick = function() { 
							var specs = <?php echo json_encode( $specs, JSON_FORCE_OBJECT ); ?>
								, tag = '<?php echo $tag ?>'
								, tag_end = '[/tag]'.replace( 'tag', tag )
								, tag_start = '[tag]'.replace( 'tag', tag )
							;
							/* Wrap or replace */
							ed.selection.setContent( specs.wrap
								? tag_start + ed.selection.getContent() + tag_end
								: tag_start 
							);
						};
					<?php } ?>
					/* Add button */
					ed.addButton( tag, specs );
				<?php } ?>
			}
		});
		/* Start the buttons */
		tinymce.PluginManager.add( 'skelet', tinymce.plugins.skelet );
	})();<?php
	return ob_get_clean();
}

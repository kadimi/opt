<?php

/**
 * @package skelet
 */

// Load the TinyMCE plugin 
add_filter( 'mce_external_plugins', 'skelet_add_tinyMCE_plugin' );
function skelet_add_tinyMCE_plugin() {
	return array( 'skelet' => site_url( '?skelet=tinyMCE_js' ) );
}

// Add endpoints
add_action( 'init', 'skelet_add_endpoint' );
function skelet_add_endpoint() {

	$rules = array(
		'tinyMCE\.js' => 'tinyMCE_js',
		'tinyMCE\.php/([a-zA-Z_][a-zA-Z0-9_-]*)' => 'tinyMCE_php&tag=$matches[1]',
	);
	
	foreach ($rules as $regex => $redirect ) {
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
		die( call_user_func( 'skelet_' . $serve, $tag ) );
	case 'tinyMCE_js':
		header( 'Content-Type: application/javascript; charset=utf-8' );
		die(
			trim( 
				preg_replace( '#\s+#', ' ',                // Removes multiple spaces
					preg_replace( '#\/\*([^*])*\*\/#', '', // Removes comments like /* ... */
						call_user_func( 'skelet_' . $serve )
					)
				)
			)
		);
	}
}

// Add buttons to WordPress editor
add_filter( 'mce_buttons', 'skelet_tinyMCE_buttons' );
function skelet_tinyMCE_buttons( $buttons ) {

	$shortcodes = K::get_var( 'paf_shortcodes', $GLOBALS, array() );
	return array_merge( $buttons, array_keys( $shortcodes ) );
}

// output for "skelet/tinyMCE.php/$tag"
function skelet_tinyMCE_php( $tag ) {
	global $paf_shortcodes;
	
	$select2_enqueued = false;
	$protocol = is_ssl() ? 'https' : 'http';

	ob_start();

	// Head
	printf( '<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title></title>' );

	// CSS
	printf( '<link rel="stylesheet" href="%s" />', admin_url( 'css/common' . ( is_rtl() ? '-rtl' : '' ) . '.css' ) );
	printf( '<link rel="stylesheet" href="%s" />', admin_url( 'css/forms' . ( is_rtl() ? '-rtl' : '' ) . '.css' ) );
	printf( '<link rel="stylesheet" href="%s" />', site_url( 'wp-includes/css/dashicons' . ( is_rtl() ? '-rtl' : '' ) . '.css' ) );
	printf( '<link rel="stylesheet" href="%s" />', site_url( 'wp-includes/css/buttons' . ( is_rtl() ? '-rtl' : '' ) . '.css' ) );
	print( '<style>body { height: auto; margin: 0; min-width: 0; padding: 1em; }</style>' );
	paf_asset_css( 'paf' );

	// JS
	printf( '<script src="%s"></script>', "$protocol://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.1/jquery.min.js" );
	printf( '<script src="%s"></script>', "$protocol://cdnjs.cloudflare.com/ajax/libs/jquery.serializeJSON/1.2.0/jquery.serializeJSON.min.js" );

	paf_asset_js( 'paf' );
	?>
	<script>
		var paf;
		var shortcode = '';
		var wrap = <?php echo K::get_var( 'wrap', $paf_shortcodes[ $tag ] ) ? 'true' : 'false'; ?>;

		jQuery( document ).ready( function ( $ ) {

			/* Fill content field with selected text if any */
			var content = parent.tinymce.activeEditor.selection.getContent( { format : 'text' } );
			$( '#content' ).val( content );

			// Update shortcode
			$( 'input,select,textarea', 'form' ).on( 'change keyup', function() { $( 'form' ).change(); } );

			// Autoselect shortcode
			$( "#shortcode" ).mouseover( function() { $( this ).select(); } );

			/**
			 * Bind to form events
			 *
			 * - On submit: Fill the WP editor
			 * - On change: update the shortcode value
			 */
			$( 'form' ).on( 'submit change', function( e ) {

				e.preventDefault();

				shortcode = '';
				paf = $( this ).serializeJSON().paf;
				content = $( '#content' ).val();

				// Build the shortcode
				Object.keys( paf ).map( function(v) {
					if( 'undefined' !== paf[ v ] && paf[ v ] ) {
						shortcode += ' '
							+ v
							+ '="'
							+ paf[ v ].toString().split().join().replace( '"', '\\"', 'g' )
							+ '"'
						;
					}
				} );
				
				if( wrap ) {
					shortcode = "[<?php echo $tag; ?>" + shortcode + "]"
						+ content
						+ "[/<?php echo $tag; ?>]"
					;
				} else {
					shortcode = "[<?php echo $tag; ?>" + shortcode + "]";
				}

				// Update the demo
				$( "#shortcode" ).val( shortcode );

				// Fill the editor and close
				if ( 'submit' === e.type ) {
					parent.tinymce.activeEditor.execCommand( "mceInsertContent", false, shortcode );
					parent.tinymce.activeEditor.windowManager.close( window );
				}
			} ).change();
		} );
	</script>
	<?php

	// Close head
	print( '</head><body class="wp-core-ui">' );

	// Fields
	$parameters = $paf_shortcodes[ $tag ]['parameters'];
	echo '<form id="paf-form" action = "">';
	foreach ( $parameters as $k => $v ) {
		// Validate title
		$v[ 'title' ] = k::get_var( 'title', $v, $k );
		// Print option
		paf_print_option( $k, $v );
		if( 'select' === K::get_var( 'type', $v ) ) {
			printf( '<link rel="stylesheet" href="%s" />', "$protocol://cdnjs.cloudflare.com/ajax/libs/select2/3.5.0/select2.min.css" );
			printf( '<script src="%s"></script>', "$protocol://cdnjs.cloudflare.com/ajax/libs/select2/3.5.0/select2.js" );
			print( "<script>jQuery( document ).ready( function( $ ) { $( 'select' ).select2(); } );</script>" );
			$select2_enqueued = true;
		}
	}

	// Content field
	if ( K::get_var( 'wrap', $paf_shortcodes[ $tag ] ) ) {
		K::textarea( 'content'
			, array( 'class' => 'large-text', 'id' => 'content' )
			, array( 'format' => '<p><label><strong>Content:</strong>:textarea</label></p>' )
		);
	}

	// Buttons
	echo '<hr />';
	echo '<p><label><strong>Shortcode:</strong><input type="text" class="large-text" id="shortcode" value=""/></label></p>';
	echo '<hr />';
	echo '<p>';
	K::input( 'submit'
		, array(
			'class' => 'button button-large button-primary aligncenter	',
			'type' => 'submit',
			'value' => 'Add shortcode',
		)
	);
	echo ' ';
	K::wrap( 'Reset'
		,array(
			'class' => 'button button-large paf-reset',
			'href' => '#',
			'id' => 'paf-reset',
		)
		, array( 'in' => 'a' )
	);
	echo '</p>';

	// Close document
	echo '</form></body></html>';

	// C ya!
	return ob_get_clean();
}

// Output for "skelet/tinyMCE.js"
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
							var specs = <?php echo json_encode( $specs, JSON_FORCE_OBJECT ); ?>;
							var tag = '<?php echo $tag ?>';
							var h; 
							var w;

							if ( 'undefined' !== typeof( specs.height ) && specs.height < 1 ) {
								h = $( window ).height() * specs.height;
							} else {
								h = $( window ).height() / 2;
							}

							if ( 'undefined' !== typeof( specs.width ) && specs.width < 1 ) {
								w = $( window ).width() * specs.width;
							} else {
								w = $( window ).width() / 2;
							}

							ed.windowManager.open( {
								title: specs.title,
								text: specs.text,
								url: '<?php echo site_url( "?skelet=tinyMCE_php&tag=$tag" ); ?>',
								width: w,
								height: h
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

/**
 * Bind shortcodes to fuctions
 *
 * For each shortcode, the function will try functions in this order:
 * - the func parameter
 * - the tag with _func added to it
 * - the tag
 */
add_action( 'init', 'skelet_process_shortcodes' );
function skelet_process_shortcodes() {
	foreach ( K::get_var( 'paf_shortcodes', $GLOBALS, array() ) as $tag => $specs ) {
		// Get func
		$func = K::get_var( 'func', $specs );
		if ( ! function_exists( $func ) ) {
			$func = $tag . '_func';
			if ( ! function_exists( $func ) ) {
				$func = $tag;
				if ( ! function_exists( $func ) ) {
					$func = 'skelet_func';
				}
			}
		}
		// bind
		add_shortcode( $tag, $func );
	}
}

// Callback used for a shortcode when non is defined for it
function skelet_func() {
	$args[ 'atts' ] = func_get_arg( 0 );
	$args[ 'content' ] = func_get_arg( 1 );
	$tag = func_get_arg( 2 );

	if( $args[ 'atts' ] ) {
		$atts = substr( json_encode( $args[ 'atts' ], JSON_PRETTY_PRINT ), 2, -2);
	} else {
		$atts = '    ' . htmlspecialchars( '<' . __( 'none' ) . '>' );
	}

	if( $args[ 'content' ] ) {
		$content = '    "' . $args[ 'content' ] . '"';
	} else {
		$content = '    ' . htmlspecialchars( '<' . __( 'none' ) . '>' );
	}

	$ret = '<pre>'
		. sprintf( __( 'Shortcode <strong>%s</strong> used with parameters:' ) , $tag )
		. "\n"
		. $atts
		. "\n"
		. __( 'With this enclosed content:' )
		. "\n"
		. $content
		. '</pre>'
	;

	return $ret;
}
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
		die( call_user_func( 'skelet_' . $serve, $tag ) );
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

	// Head
	printf( '<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title></title>' );

	// Add JS and CSS

	// CSS
	printf( '<link rel="stylesheet" href="%s" />', admin_url( 'css/common' . ( is_rtl() ? '-rtl' : '' ) . '.css' ) );
	printf( '<link rel="stylesheet" href="%s" />', admin_url( 'css/forms' . ( is_rtl() ? '-rtl' : '' ) . '.css' ) );
	printf( '<link rel="stylesheet" href="%s" />', site_url( 'wp-includes/css/buttons' . ( is_rtl() ? '-rtl' : '' ) . '.css' ) );
	print( '<style>body {
		height: auto;
		margin: 0;
		min-width: 0;
		padding: 1em;
	}</style>'
	);
	paf_asset_css( 'paf' );

	// JS
	printf( '<script src="%s"></script>', "$protocol://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.1/jquery.min.js" );
	paf_asset_js( 'paf' );
	?>
	<script>
		var paf;
		var shortcode = '';
		jQuery( document ).ready( function ( $ ) {

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
				shortcode = "[<?php echo $tag?>" + shortcode + "]";

				// Update the demo
				$( "#shortcode" ).val( shortcode );

				// Fill the editor and close
				if ( 'submit' === e.type ) {
					parent.tinymce.activeEditor.execCommand( "mceInsertContent", false, shortcode );
					parent.tinymce.activeEditor.windowManager.close( window );
				}
			} ).change();
		} );
		
		/*!
		SerializeJSON jQuery plugin.
		https://github.com/marioizquierdo/jquery.serializeJSON
		version 2.4.1 (Oct, 2014)
		Copyright (c) 2014 Mario Izquierdo
		Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
		and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
		*/
		(function(a){a.fn.serializeJSON=function(k){var g,e,j,h,i,c,d,b;d=a.serializeJSON;b=d.optsWithDefaults(k);d.validateOptions(b);e=this.serializeArray();d.readCheckboxUncheckedValues(e,this,b);g={};a.each(e,function(l,f){j=d.splitInputNameIntoKeysArray(f.name);h=j.pop();if(h!=="skip"){i=d.parseValue(f.value,h,b);if(b.parseWithFunction&&h==="_"){i=b.parseWithFunction(i,f.name)}d.deepSet(g,j,i,b)}});return g};a.serializeJSON={defaultOptions:{parseNumbers:false,parseBooleans:false,parseNulls:false,parseAll:false,parseWithFunction:null,checkboxUncheckedValue:undefined,useIntKeysAsArrayIndex:false},optsWithDefaults:function(c){var d,b;if(c==null){c={}}d=a.serializeJSON;b=d.optWithDefaults("parseAll",c);return{parseNumbers:b||d.optWithDefaults("parseNumbers",c),parseBooleans:b||d.optWithDefaults("parseBooleans",c),parseNulls:b||d.optWithDefaults("parseNulls",c),parseWithFunction:d.optWithDefaults("parseWithFunction",c),checkboxUncheckedValue:d.optWithDefaults("checkboxUncheckedValue",c),useIntKeysAsArrayIndex:d.optWithDefaults("useIntKeysAsArrayIndex",c)}},optWithDefaults:function(c,b){return(b[c]!==false)&&(b[c]!=="")&&(b[c]||a.serializeJSON.defaultOptions[c])},validateOptions:function(d){var b,c;c=["parseNumbers","parseBooleans","parseNulls","parseAll","parseWithFunction","checkboxUncheckedValue","useIntKeysAsArrayIndex"];for(b in d){if(c.indexOf(b)===-1){throw new Error("serializeJSON ERROR: invalid option '"+b+"'. Please use one of "+c.join(","))}}},parseValue:function(g,b,c){var e,d;d=a.serializeJSON;if(b=="string"){return g}if(b=="number"||(c.parseNumbers&&d.isNumeric(g))){return Number(g)}if(b=="boolean"||(c.parseBooleans&&(g==="true"||g==="false"))){return(["false","null","undefined","","0"].indexOf(g)===-1)}if(b=="null"||(c.parseNulls&&g=="null")){return["false","null","undefined","","0"].indexOf(g)!==-1?null:g}if(b=="array"||b=="object"){return JSON.parse(g)}if(b=="auto"){return d.parseValue(g,null,{parseNumbers:true,parseBooleans:true,parseNulls:true})}return g},isObject:function(b){return b===Object(b)},isUndefined:function(b){return b===void 0},isValidArrayIndex:function(b){return/^[0-9]+$/.test(String(b))},isNumeric:function(b){return b-parseFloat(b)>=0},splitInputNameIntoKeysArray:function(c){var e,b,d,h,g;g=a.serializeJSON;h=g.extractTypeFromInputName(c),b=h[0],d=h[1];e=b.split("[");e=a.map(e,function(f){return f.replace(/]/g,"")});if(e[0]===""){e.shift()}e.push(d);return e},extractTypeFromInputName:function(c){var b,d;d=a.serializeJSON;if(b=c.match(/(.*):([^:]+)$/)){var e=["string","number","boolean","null","array","object","skip","auto"];if(e.indexOf(b[2])!==-1){return[b[1],b[2]]}else{throw new Error("serializeJSON ERROR: Invalid type "+b[2]+" found in input name '"+c+"', please use one of "+e.join(", "))}}else{return[c,"_"]}},deepSet:function(c,l,j,b){var k,h,g,i,d,e;if(b==null){b={}}e=a.serializeJSON;if(e.isUndefined(c)){throw new Error("ArgumentError: param 'o' expected to be an object or array, found undefined")}if(!l||l.length===0){throw new Error("ArgumentError: param 'keys' expected to be an array with least one element")}k=l[0];if(l.length===1){if(k===""){c.push(j)}else{c[k]=j}}else{h=l[1];if(k===""){i=c.length-1;d=c[i];if(e.isObject(d)&&(e.isUndefined(d[h])||l.length>2)){k=i}else{k=i+1}}if(e.isUndefined(c[k])){if(h===""){c[k]=[]}else{if(b.useIntKeysAsArrayIndex&&e.isValidArrayIndex(h)){c[k]=[]}else{c[k]={}}}}g=l.slice(1);e.deepSet(c[k],g,j,b)}},readCheckboxUncheckedValues:function(e,d,i){var b,h,g,c,j;if(i==null){i={}}j=a.serializeJSON;b="input[type=checkbox][name]:not(:checked)";h=d.find(b).add(d.filter(b));h.each(function(f,k){g=a(k);c=g.attr("data-unchecked-value");if(c){e.push({name:k.name,value:c})}else{if(!j.isUndefined(i.checkboxUncheckedValue)){e.push({name:k.name,value:i.checkboxUncheckedValue})}}})}}}(window.jQuery||window.Zepto||window.$));
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
		if( 'select' === $v[ 'type' ] ) {
			printf( '<link rel="stylesheet" href="%s" />', "$protocol://cdnjs.cloudflare.com/ajax/libs/select2/3.5.0/select2.min.css" );
			printf( '<script src="%s"></script>', "$protocol://cdnjs.cloudflare.com/ajax/libs/select2/3.5.0/select2.js" );
			print( "<script>jQuery( document ).ready( function( $ ) { $( 'select' ).select2(); } );</script>" );
			$select2_enqueued = true;
		}
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

	// Close form
	echo '</form>';
	echo '</body></html>';

	// C ya!
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

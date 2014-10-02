<?php

/**
 * Main core file of the "Plugin Options" framework
 * 
 * The file loads the different parts of the framework
 * 
 * @package plugin-admin-framework
 */

/**
 * Helps tracking that the framework core was loaded
 *
 * @var bool
 */
define( 'PLUGIN_OPTIONS', 1 );

/**
 * Load the class K
 */
if ( ! class_exists( 'K' ) ) {
	require dirname( __FILE__ ) . '/lib/K/K.php';
}

/**
 * Load the class Kint
 */
if ( ! class_exists ( 'Kint' ) ) {
    require dirname( __FILE__ ) . '/lib/kint/Kint.class.php';
}

/**
 * Include options files
 */
foreach ( array( 'pages', 'tabs', 'options' ) as $option_file_name ) {
	require dirname( __FILE__ ) . '/../data/' . $option_file_name . '.php';
}

/**
 * Include core files
 */
foreach ( array( 'pages', 'options' ) as $core_file_name ) {
	include dirname( __FILE__ ) . '/core-' . $core_file_name . '.php';
}

/**
 * Add JS file
 */
function paf_asset_js( $asset, $block = FALSE ) {

	// Trac files that are blocked in the futire
	static $blocked = array();

	$js[] = 'paf';

	// Do nothing if asset not defined
	if ( ! in_array( $asset, $js ) ) {
		return;
	}

	// Do nothing if already loaded
	if( in_array( $asset, $blocked ) ) {
		return;
	}

	// Mark as blocked if needed
	if( $block ) {
		$blocked[] = $asset;
	}

	// Print asset
	$src = dirname( __FILE__) . '/../assets/js/' . $asset . '.js';
	printf( '<script>%s</script>', file_get_contents( $src ) );
}

function paf_url() {
	return 'http'
		. ( is_ssl() ? 's' : '' )
		. '://'
		. $_SERVER[ 'SERVER_NAME' ]
		. ( 80 != $_SERVER[ 'SERVER_PORT' ] ? ":$_SERVER[SERVER_PORT]" : '' )
		. $_SERVER[ 'REQUEST_URI' ]
	;
}

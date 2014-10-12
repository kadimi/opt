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
 * Adds some JS in the header:
 * - paf_assets, here paf assets can be found
 */
function paf_header() {

	$home_path = get_home_path();
	$assets_path = str_replace(
		array( $home_path, 'core/core.php' )
		, array( '', 'assets/' )
		, __FILE__
	);
	
	$assets_dir_url = home_url( $assets_path );
	printf(
		'<script>var paf_assets = "%s"</script>'
		, $assets_dir_url
	);
}
add_action( 'admin_head', 'paf_header' );

/**
 * Add JS file
 */
function paf_asset_js( $asset, $block = FALSE ) {

	return paf_asset( $asset, 'js', $block );
}

/**
 * Add CSS file
 */
function paf_asset_css( $asset, $block = FALSE ) {

	return paf_asset( $asset, 'css', $block );
}

/**
 * Add asset
 */
function paf_asset( $asset, $type, $block = FALSE ) {

	// Trac files that are blocked in the futire
	static $blocked = array();

	// Exit function if type is not supported
	if( ! in_array( $type, array( 'css', 'js' ) ) ) {
		return;
	}

	$js[] = 'paf';
	$css[] = 'paf';

	// Do nothing if asset not defined
	if ( ! in_array( $asset, $$type ) ) {
		return;
	}

	// Do nothing if already loaded
	if( in_array( "$type/$asset", $blocked ) ) {
		return;
	}

	// Mark as blocked if needed
	if( $block ) {
		$blocked[] = "$type/$asset";
	}

	// Print asset
	$src = dirname( __FILE__) . "/../assets/$type/$asset.$type";
	printf( '<%s>%s</%s>'
		, 'css' === $type ? 'style' : 'script'
		, file_get_contents( $src )
		, 'css' === $type ? 'style' : 'script'
	);
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

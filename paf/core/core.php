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
 * Load important gobal data
 * 
 * The data is:
 *  - paf_options
 *  - paf_pages
 *  - paf_tabs
 *  - paf_page_tabs
 *  - paf_page_sections
 *  - paf_page_options
 *  - paf_page
 *  - paf_tab
 */
function paf_load() {

	global $paf_options, $paf_pages, $paf_tabs;
	foreach ( array( 'options', 'pages', 'tabs' ) as $k ) {
		$k = 'paf_' . $k;
		if ( ! K::get_var( $k, $GLOBALS ) ) {
			$GLOBALS[ $k ] = call_user_func( $k );
		}
	}

	global $paf_page_tabs, $paf_page_sections, $paf_page_options;
	$paf_page_tabs = $paf_page_sections = $paf_page_options = array();

	global $paf_page, $paf_tab;
	$paf_page = K::get_var( 'page', $_GET );
	$paf_tab = K::get_var( 'tab', $_GET );

	// Get defined page tabs
	foreach ( $paf_tabs as $slug => $page_tab ) {
		if( $paf_page === $paf_tabs[ $slug ][ 'page' ] ) {
			$paf_page_tabs[ $slug ] = $page_tab;
		}
	}

	// Get defined page sections
	foreach ( $paf_options as $id => $page_option ) {
		if ( $paf_page === $page_option[ 'page' ] && K::get_var( 'section', $page_option ) ) {
			$paf_page_sections[ $page_option[ 'section' ] ] = $page_option[ 'section_title' ];
		}
	}

	/**
	 * Use the first tab:
	 *   - if page has tabs and none is specified in $_GET
	 *   - or if the specified tab doesn't exist
	 */
	if(
		( $paf_page_tabs && ! $paf_tab )
		|| ( $paf_page_tabs && $paf_tab && ! $paf_page_tabs[ $paf_tab ] )
	) {
		reset( $paf_page_tabs );
		$paf_tab = key( $paf_page_tabs );
	}

	// If the page has a tab, force tab-less options to use the default tab
	reset( $paf_options );
	foreach ( $paf_options as $id => $paf_option ) {
		if( $paf_page === $paf_option[ 'page' ] ) {
			if( $paf_tab && ! K::get_var( 'tab', $paf_option ) ) {
				$paf_options[ $id ][ 'tab' ] = key( $paf_page_tabs );
			}
		}
	}

	// Get defined page and tab options
	reset( $paf_options );
	foreach ( $paf_options as $id => $paf_option ) {
		if( $paf_page === $paf_option[ 'page' ] ) {
			if( ! $paf_tab || ( $paf_tab === $paf_option[ 'tab' ] ) ) {
				$paf_page_options[ $id ] = $paf_option;
			}
		}
	}
}
add_action( 'admin_init', 'paf_load' );

/**
 * Save options
 * 
 * The function will preserve options saved on other pages
 */
function paf_save() {

	// Do not save if the nonce is not valid
	if ( ! wp_verify_nonce( K::get_var( 'paf_nonce', $_POST ), 'paf_save' ) ) {

		return;
	} else {

		// Combine old and saved options
		$paf = get_option( 'paf ', array() );
		$paf = array_merge( $paf, $_POST[ 'paf' ] );
		// Save
		delete_option( 'paf' );
		add_option( 'paf', $paf, '', TRUE );
		// Show success message
		add_action( 'admin_notices', 'paf_notice' );
	}
}
add_action( 'admin_init', 'paf_save' );

/**
 * Show message when options are saved successfully
 * 
 * Seeks in this order: tab > page > default
 */
function paf_notice() {

	global $paf_pages, $paf_tabs, $paf_page, $paf_tab;

	// Look in tab configuration
	if ( $paf_tab && $message = K::get_var( 'success', $paf_tabs[ $paf_tab ] ) ) {}
	// Look in page configuration
	else if ( $message = K::get_var( 'success', $paf_pages[ $paf_page ] ) ) {}
	// Use default
	else { $message = __( 'Settings saved.'); }

	printf(
		'<div class="updated"><p>%s</p></div>'
		, $message
	);
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

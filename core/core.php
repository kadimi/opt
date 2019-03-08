<?php

/**
 * Main core file of the "Plugin Options" framework
 * 
 * The file loads the different parts of the framework
 * 
 * @package opt
 */

/**
 * Helps tracking that the framework core was loaded
 *
 * @var bool
 */
define( 'OPT', 1 );

/**
 * Load the class K
 */
if ( ! class_exists( 'K' ) ) {

	require dirname( __FILE__ ) . '/lib/K/K.php';
}

/**
 * Load JSMin and CSSmin
 */
if ( ! class_exists( 'JSMin' ) ) {

	require dirname( __FILE__ ) . '/lib/JSMin.php';
}
if ( ! class_exists( 'CSSmin' ) ) {

	require dirname( __FILE__ ) . '/lib/CSSmin.php';
}

/**
 * Include core files
 */
foreach ( array( 'pages', 'options', 'shortcodes' ) as $core_file_name ) {

	include dirname( __FILE__ ) . '/core-' . $core_file_name . '.php';
}

/**
 * Enqueue JS/CSS
 */
function opt_enqueue() {

	$protocol = 'http' . ( is_ssl() ? 's' : '' );

	$js = array(
		'highlight' => "$protocol://cdn.jsdelivr.net/npm/highlightjs@9.12.0/highlight.pack.min.js",
	);

	$css = array(
		'highlight' => "$protocol://cdn.jsdelivr.net/npm/highlightjs@9.12.0/styles/default.css",
	);

	foreach ( $js as $handle => $src ) {
		wp_enqueue_script( $handle, $src );
	}
	foreach ( $css as $handle => $src ) {
		wp_enqueue_style( $handle, $src );
	}
}
add_action( 'admin_init', 'opt_enqueue' );

/**
 * Load important gobal data
 * 
 * The data is:
 *  - opt
 *  - opt_page_tabs
 *  - opt_page_options
 *  - opt_page_sections
 *  - opt_page_shortcodes
 *  - opt_page
 *  - opt_tab
 */
function opt_load() {

	global $opt;
	$opt = get_option( 'opt', array() );

	global $opt_options, $opt_pages, $opt_sections, $opt_shortcodes, $opt_tabs;

	// Make sure $GLOBALS[ 'opt_...' ] exist
	foreach ( array( 'opt_options', 'opt_pages', 'opt_sections', 'opt_shortcodes', 'opt_tabs' ) as $k ) {
		if( empty( $GLOBALS[ $k ] ) ) {
			$GLOBALS[ $k ] = array();
		}
	}

	global $opt_page_tabs, $opt_page_options, $opt_page_sections;
	$opt_page_tabs = $opt_page_options = $opt_page_sections = array();

	global $opt_page, $opt_tab;
	$opt_page = K::get_var( 'page', $_GET );
	$opt_tab = K::get_var( 'tab', $_GET );

	// Get defined page tabs
	foreach ( $opt_tabs as $slug => $page_tab ) {
		if( $opt_page === $opt_tabs[ $slug ][ 'page' ] ) {
			$opt_page_tabs[ $slug ] = $page_tab;
		}
	}

	/**
	 * Use the first tab:
	 *   - if page has tabs and none is specified in $_GET
	 *   - or if the specified tab doesn't exist
	 */
	if(
		( $opt_page_tabs && ! $opt_tab )
		|| ( $opt_page_tabs && $opt_tab && ! K::get_var( $opt_tab, $opt_page_tabs ) )
	) {
		reset( $opt_page_tabs );
		$opt_tab = key( $opt_page_tabs );
	}

	// If the page has a tab, force tab-less options to use the default tab
	reset( $opt_options );
	foreach ( $opt_options as $id => $opt_option ) {
		if( $opt_page === K::get_var( 'page', $opt_option ) ) {
			if( $opt_tab && ! K::get_var( 'tab', $opt_option ) ) {
				$opt_options[ $id ][ 'tab' ] = key( $opt_page_tabs );
			}
		}
	}

	// Get defined page and tab options
	reset( $opt_options );
	foreach ( $opt_options as $id => $opt_option ) {
		if( $opt_page === K::get_var( 'page', $opt_option ) ) {
			if( ! $opt_tab || ( $opt_tab === $opt_option[ 'tab' ] ) ) {
				$opt_page_options[ $id ] = $opt_option;
			}
		}
	}

	// Get defined page and tab sections
	reset( $opt_page_options );
	foreach ( $opt_page_options as $id => $opt_option ) {
		if ( K::get_var( 'section', $opt_option ) ) {
			$opt_page_sections[ $opt_option[ 'section' ] ] = K::get_var( $opt_option[ 'section' ], $opt_sections, array() );
		}
	}
}
add_action( 'admin_init', 'opt_load' );

/**
 * Save options
 * 
 * The function will preserve options saved on other pages
 */
function opt_save() {

	global $opt_page_options;

	// Abort saving if the nonce is not valid
	if ( ! wp_verify_nonce( K::get_var( 'opt_nonce', $_POST ), 'opt_save' ) ) {

		return;
	}

	// Force select and radio to have a value to prevent skipping empty
	// Escape text on textual fields
	$_POST[ 'opt' ] = K::get_var( 'opt', $_POST, array() );
	$_POST[ 'opt' ] = stripslashes_deep( $_POST[ 'opt' ] );
	foreach ( $opt_page_options as $option_id => $option_def ) {
		$option_type = K::get_var( 'type', $option_def, 'text' );
		switch ( $option_type ) {
			case 'text':
			case 'textarea':
				$_POST['opt'][ $option_id ] = esc_attr( K::get_var( $option_id, $_POST['opt'], '' ) );
				break;
			case 'media':
				$_POST['opt'][ $option_id ] = esc_url( K::get_var( $option_id, $_POST['opt'], '' ) );
				break;
			case 'radio':
				$_POST['opt'][ $option_id ] = K::get_var( $option_id, $_POST['opt'], '' );
				break;
			case 'checkbox':
			case 'select':
				$_POST['opt'][ $option_id ] = K::get_var( $option_id, $_POST['opt'], array() );
				break;
		}
	}

	// Combine old and saved options
	$opt = get_option( 'opt ', array() );
	$opt = array_merge( $opt, $_POST[ 'opt' ] );

	// Save
	delete_option( 'opt' );
	add_option( 'opt', $opt, '', TRUE );

	// Bind showing the success message
	add_action( 'admin_notices', 'opt_notice' );
}
add_action( 'admin_init', 'opt_save' );

/**
 * Add $pages to the global $opt_pages
 */
function opt_pages( $pages ) {

	$GLOBALS[ 'opt_pages'] = array_merge( K::get_var( 'opt_pages', $GLOBALS, array() ) , $pages );
}

/**
 * Add $options to the global $opt_options
 */
function opt_options( $options ) {

	$GLOBALS[ 'opt_options'] = array_merge( K::get_var( 'opt_options', $GLOBALS, array() ) , $options );
}

/**
 * Add $tabs to the global $opt_tabs
 */
function opt_tabs( $tabs ) {

	$GLOBALS[ 'opt_tabs'] = array_merge( K::get_var( 'opt_tabs', $GLOBALS, array() ) , $tabs );
	// ksort( $GLOBALS[ 'opt_tabs' ] );
}

/**
 * Add $sections to the global $opt_sections
 */
function opt_sections( $sections ) {

	$GLOBALS[ 'opt_sections'] = array_merge( K::get_var( 'opt_sections', $GLOBALS, array() ) , $sections );
	// ksort( $GLOBALS[ 'opt_sections' ] );
}

/**
 * Add $sections to the global $opt_sections
 */
function opt_shortcodes( $shortcodes ) {

	$GLOBALS[ 'opt_shortcodes' ] = array_merge( K::get_var( 'opt_shortcodes', $GLOBALS, array() ) , $shortcodes );
	// ksort( $GLOBALS[ 'opt_shortcodes' ] );
}

/**
 * Show message when options are saved successfully
 * 
 * Seeks in this order: tab > page > default
 */
function opt_notice() {

	global $opt_pages, $opt_tabs, $opt_page, $opt_tab;

	// Look in tab configuration
	if ( $opt_tab && $message = K::get_var( 'success', $opt_tabs[ $opt_tab ] ) ) {}
	// Look in page configuration
	else if ( $message = K::get_var( 'success', $opt_pages[ $opt_page ] ) ) {}
	// Use default
	else { $message = __( 'Settings saved.'); }

	printf(
		'<div class="updated"><p>%s</p></div>'
		, $message
	);
}

/**
 * Adds some JS in the header:
 * - opt_assets, here opt assets can be found
 */
function opt_header() {

	$home_path = get_home_path();
	$assets_path = str_replace(
		array( $home_path, 'core/core.php' )
		, array( '', 'assets/' )
		, __FILE__
	);
	
	$assets_dir_url = home_url( $assets_path );
	?>
		<script>
			var opt_assets = "<?php echo $assets_dir_url ?>";
			hljs.initHighlightingOnLoad();
		</script>
	<?php
}
add_action( 'admin_head', 'opt_header' );

/**
 * Add JS file
 */
function opt_asset_js( $asset, $block = FALSE ) {

	return opt_asset( $asset, 'js', $block );
}

/**
 * Add CSS file
 */
function opt_asset_css( $asset, $block = FALSE ) {

	return opt_asset( $asset, 'css', $block );
}

/**
 * Add asset
 */
function opt_asset( $asset, $type, $block = FALSE ) {

	// Trac files that are blocked in the futire
	static $blocked = array();

	// Exit function if type is not supported
	if( ! in_array( $type, array( 'css', 'js' ) ) ) {
		return;
	}

	$js[] = 'opt';
	$css[] = 'opt';

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

	// Get source
	$src = dirname( __FILE__) . "/../assets/$type/$asset.$type";
	$o = file_get_contents( $src );

	// Minify source
	switch ( $type ) {
	case 'css':
		$CSSmin = new CSSmin();
		$o = $CSSmin->run( $o );
	case 'js':
		$o = JSMin::minify( $o );
	}

	// Wrap in tags
	$o = sprintf( '<%s>%s</%s>'
		, 'css' === $type ? 'style' : 'script'
		, $o
		, 'css' === $type ? 'style' : 'script'
	);

	// Output
	print( $o );
}

function opt_url() {

	return 'http'
		. ( is_ssl() ? 's' : '' )
		. '://'
		. $_SERVER[ 'SERVER_NAME' ]
		. ( 80 != $_SERVER[ 'SERVER_PORT' ] ? ":$_SERVER[SERVER_PORT]" : '' )
		. $_SERVER[ 'REQUEST_URI' ]
	;
}

function opt_htmlspecialchars_recursive( &$array ) {

	$array = htmlspecialchars( $array );
}

function opt_dir( $dir ) {
	foreach ( array( 'pages', 'tabs', 'sections', 'options', 'shortcodes' ) as $option_file_name ) {

		$option_file_path = $dir . '/' . $option_file_name . '.php';
		if ( file_exists( $option_file_path ) ) {
			require $option_file_path;
		}
	}
}

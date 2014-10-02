<?php

/**
 * @package plugin-admin-framework
 */
add_action( 'admin_menu', 'paf_admin_add_pages' );

/**
 * Adds top level pages and subpages
 */
function paf_admin_add_pages() {

	$paf_pages = paf_pages();

	foreach ( $paf_pages as $slug => $paf_page ) {

		// Add top level menu pages
		if( ! $paf_page[ 'parent' ] ) {
			add_menu_page(
				$paf_page[ 'title' ]
				, $paf_page[ 'menu_title' ]
				, 'manage_options'
				, $slug
				, 'paf_page_cb'
				, $paf_page[ 'icon_url' ]
				, $paf_page[ 'position' ]
			);
		}

		// Add sub menu pages
		if( $paf_page[ 'parent' ] ) {
			add_submenu_page(
				$paf_page[ 'parent' ]
				, $paf_page[ 'title' ]
				, $paf_page[ 'menu_title' ]
				, 'manage_options'
				, $slug
				, 'paf_page_cb'
			);
		}
	}
}

/**
 * Callback function for pages
 */
function paf_page_cb() {

	/**
	 *	Fill $paf_options, $paf_pages, $paf_tabs in one shot
	 */
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

	echo '<div class="wrap"><h2>' . $paf_page . '</h2>';

	// Print tabs links
	if( $paf_page_tabs ) {
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $paf_page_tabs as $slug => $page_tab) {
			printf( '<a href="?page=%s&amp;tab=%s" class="nav-tab %s">%s</a>'
				, $paf_page
				, $slug
				, ( $paf_tab === $slug ) ? 'nav-tab-active' : ''
				, $page_tab[ 'menu_title' ]
			);
		}
		echo '</h2>';
		echo '<h2>' . $paf_page_tabs [ $paf_tab ][ 'title' ] . '</h2>';
	}

	// Print the options

	echo '<form action="' . paf_url() . '" method="post">';
	reset( $paf_page_options );
	foreach ( $paf_page_options as $id => $page_option ) {
		paf_print_option( $id );
	}
	echo '</form>';

	// Print debugging information
	K::wrap(
		__( 'Debugging information' )
		, array( 'style' => 'margin-top: 5em;' )
		, array( 'in' => 'h3' )
	);

	if( $paf_page_tabs ) {
		d( $paf_pages[ $paf_page ], $paf_page_tabs, $paf_page_sections, $paf_page_options );
	} else {
		d( $paf_pages[ $paf_page ], $paf_page_sections, $paf_page_options );
	}

	echo '</div>';
}

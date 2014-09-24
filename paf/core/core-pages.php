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

	foreach ( $paf_pages as $slug => $page ) {

		// Add top level menu pages
		if( ! $page[ 'parent' ] ) {
			add_menu_page(
				$page[ 'title' ]
				, $page[ 'menu_title' ]
				, 'manage_options'
				, $slug
				, 'paf_page_cb'
				, $page[ 'icon_url' ]
				, $page[ 'position' ]
			);
		}

		// Add sub menu pages
		if( $page[ 'parent' ] ) {
			add_submenu_page(
				$page[ 'parent' ]
				, $page[ 'title' ]
				, $page[ 'menu_title' ]
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
	
	$paf_pages = paf_pages();
	$paf_tabs = paf_tabs();
	$page = $_GET[ 'page' ];
	$tab = $_GET[ 'tab' ];
	$page_tabs = array();

	// Get defined page tabs
	foreach ( $paf_tabs as $slug => $page_tab ) {
		if( $page === $paf_tabs[ $slug ][ 'page' ] ) {
			$page_tabs[ $slug ] = $page_tab;
		}
	}

	/**
	 * Use the first tab:
	 *   - if page has tabs and noe is specified in $_GET
	 *   - or if the specified tab doesn't exist
	 */
	if(
		( $page_tabs && ! $tab )
		|| ( $page_tabs && $tab && ! $page_tabs[ $tab ] )
	) {
		reset( $page_tabs );
		$tab = key( $page_tabs );
	}

	echo '<div class="wrap"><h2>' . $page . '</h2>';
	echo '<pre>' . print_r( $paf_pages[ $page ], true ) . '</pre>';

	// Print tabs links
	if( $page_tabs ) {
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $page_tabs as $slug => $page_tab) {
			printf( '<a href="?page=%s&amp;tab=%s" class="nav-tab %s">%s</a>'
				, $page
				, $slug
				, ( $tab === $slug ) ? 'nav-tab-active' : ''
				, $page_tab[ 'menu_title' ]
			);
		}
		echo '</h2>';
		echo '<h2>' . $page_tabs [ $tab ][ 'title' ] . '</h2>';

	}

	echo '<hr /><h1>Tabs</h1>';

	echo '<pre>' . print_r( $page_tabs, true ) . '</pre>';
	echo '</div>';
}

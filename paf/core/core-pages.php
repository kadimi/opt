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
	$page = $_GET[ 'page' ];

	echo '<h1>' . $page . '</h1>';

	echo '<pre>' . print_r( $paf_pages[ $page ], true ) . '</pre>';
}
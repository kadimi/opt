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
		if( ! K::get_var( 'parent',  $paf_page ) ) {
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
		if( K::get_var( 'parent',  $paf_page ) ) {
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

	global $paf_options, $paf_pages, $paf_tabs;
	global $paf_page_tabs, $paf_page_sections, $paf_page_options;
	global $paf_page, $paf_tab;

	// Get submit button text (looks in: tab > page > default )
	if ( $paf_tab && $submit_button_text = K::get_var( 'submit_button', $paf_page_tabs[ $paf_tab ] ) ) {
		;
	} else if ( $submit_button_text = K::get_var( 'submit_button', $paf_pages[ $paf_page ] ) ) {
		;
	} else {
		$submit_button_text = __( 'Save Changes' );
	}

	// Start output
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
	echo '<form id="paf-form" class="hidden" action="' . paf_url() . '" method="post">';
	reset( $paf_page_options );
	foreach ( $paf_page_options as $id => $page_option ) {
		paf_print_option( $id );
	}
	wp_nonce_field( 'paf_save', 'paf_nonce' );
	submit_button( $submit_button_text, 'primary large', 'paf_submit' );
	echo '</form>';

	// Add JS and CSS
	paf_asset_js( 'paf', TRUE );
	paf_asset_css( 'paf', TRUE );

	// // Print debugging information
	// K::wrap(
	// 	__( 'Debugging information' )
	// 	, array( 'style' => 'margin-top: 5em;' )
	// 	, array( 'in' => 'h3' )
	// );

	// if( $paf_page_tabs ) {
	// 	d( $paf_pages[ $paf_page ], $paf_page_tabs, $paf_page_sections, $paf_page_options );
	// } else {
	// 	d( $paf_pages[ $paf_page ], $paf_page_sections, $paf_page_options );
	// }

	echo '</div>';
}

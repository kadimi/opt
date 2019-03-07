<?php

/**
 * @package opt
 */
add_action( 'admin_menu', 'opt_admin_add_pages' );

/**
 * Adds top level pages and subpages
 */
function opt_admin_add_pages() {

	global $opt_pages;

	// Do nothing if there are no pages
	if( empty( $opt_pages ) ) {
		return;
	}

	foreach ( $opt_pages as $slug => $opt_page ) {

		// Add top level menu pages
		if( ! K::get_var( 'parent',  $opt_page ) ) {
	
			/**
			 * Handle position, no top level menus should have the same position
			 * 
			 * - We will append a dot and the binary representation of the slug (to assure alphabetical order)
			 * - We add 5 after the dot so that menues with go to the middle, this way,
			 *   if you want a menu to go to the top use a decimal lover than .5 and vice versa
			 */
			$opt_page[ 'position' ] = K::get_var( 'position', $opt_page, null );
			if ( preg_match( '/^\d+$/' , $opt_page[ 'position' ] ) ) {
				$slug_unpacked = unpack('H*', $slug );
				$opt_page[ 'position' ] = ( string ) (
					$opt_page[ 'position' ]
					. '.5'
					. base_convert( $slug_unpacked[1], 16, 2 )
				);
			}

			add_menu_page(
				$opt_page[ 'title' ]
				, $opt_page[ 'menu_title' ]
				, 'manage_options'
				, $slug
				, 'opt_page_cb'
				, $opt_page[ 'icon_url' ]
				, $opt_page[ 'position' ]
			);
		}

		// Add sub menu pages
		if( K::get_var( 'parent',  $opt_page ) ) {
			add_submenu_page(
				$opt_page[ 'parent' ]
				, $opt_page[ 'title' ]
				, $opt_page[ 'menu_title' ]
				, 'manage_options'
				, $slug
				, 'opt_page_cb'
			);
		}
	}
}

/**
 * Callback function for pages
 */
function opt_page_cb() {

	global $opt;
	global $opt_options, $opt_pages, $opt_sections, $opt_tabs;
	global $opt_page_tabs, $opt_page_sections, $opt_page_options;
	global $opt_page, $opt_tab;

	// Get submit button text (looks in: tab > page > default )
	if ( $opt_tab && $submit_button_text = K::get_var( 'submit_button', $opt_page_tabs[ $opt_tab ] ) ) {
		;
	} else if ( $submit_button_text = K::get_var( 'submit_button', $opt_pages[ $opt_page ] ) ) {
		;
	} else {
		$submit_button_text = __( 'Save Changes' );
	}

	// Get reset button text (looks in: tab > page )
	if ( $opt_tab && $reset_button_text = K::get_var( 'reset_button', $opt_page_tabs[ $opt_tab ] ) ) {
		;
	} else if ( $reset_button_text = K::get_var( 'reset_button', $opt_pages[ $opt_page ] ) ) {
		;
	}

	// Start output
	echo '<div class="wrap">'
		. '<h2>'
		. K::get_var( 'title', $opt_pages[ $opt_page ], $opt_page )
		. '</h2>'
	;

	// Print tabs links
	if( $opt_page_tabs ) {
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $opt_page_tabs as $slug => $page_tab) {
			printf( '<a href="?page=%s&amp;tab=%s" class="nav-tab %s">%s</a>'
				, $opt_page
				, $slug
				, ( $opt_tab === $slug ) ? 'nav-tab-active' : ''
				, $page_tab[ 'menu_title' ]
			);
		}
		echo '</h2>';
		echo '<h2>' . $opt_page_tabs [ $opt_tab ][ 'title' ] . '</h2>';
	}

	// Print the options
	echo '<form id="opt-form" class="hidden" action="' . opt_url() . '" method="post">';
	
	// Show options that don't have sections
	reset( $opt_page_options );
	foreach ( $opt_page_options as $id => $page_option ) {
		if ( K::get_var( 'section', $page_option ) ) {
			continue;
		}
		opt_print_option( $id );
	}

	// Show options that have sections
	reset( $opt_page_options );
	foreach ($opt_page_sections as $section_id => $page_section ) {

		K::wrap( K::get_var( 'title', $page_section, $section_id )
			, array( 'class' => 'title' )
			, array( 'in' => 'h3' )
		);

		foreach ( $opt_page_options as $id => $page_option ) {
			if ( $section_id === K::get_var( 'section', $page_option ) ) {
				opt_print_option( $id );
			}
		}
	}

	// Nonce
	wp_nonce_field( 'opt_save', 'opt_nonce' );
	
	// Submit and Reset buttons
	echo '<p>';
	K::input(
		'opt_submit'
		, array(
			'class' => 'button button-large button-primary',
			'href' => '#',
			'id' => 'opt-submit',
			'type' => 'submit',
			'value' => $submit_button_text,
		)
		, array(
			'in' => 'input',
		)
	);
	if( $reset_button_text ) {
		echo ' ';
		K::wrap( $reset_button_text
			,array(
				'class' => 'button button-large opt-reset',
				'href' => '#',
				'id' => 'opt-reset',
			)
			, array(
				'in' => 'a'
			)
		);
	}
	echo '</p>';

	echo '</form>';

	// Add JS and CSS
	opt_asset_js( 'opt', TRUE );
	opt_asset_css( 'opt', TRUE );

	echo '</div>';
}

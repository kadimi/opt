<?php

/**
* Tabs definitions
* 
* @package plugin-admin-framework
*/

/**
 * Defines tabs for this plugin
 */
function paf_tabs() {

	/**
	 * Add tabs here
	 */
	$tabs[ 'tab_1'] = array(
		'title'      => __( 'Tab one' ),
		'menu_title' => __( 'Tab 1' ),
		'page'       => __( 'page_a' ),
	);
	$tabs[ 'tab_2'] = array(
		'title'      => __( 'Tab two' ),
		'menu_title' => __( 'Tab 2' ),
		'page'       => __( 'page_a' ),
	);

	/**
	 * Do not touch this line
	 */
	return empty( $tabs ) ? array() : $tabs;
}

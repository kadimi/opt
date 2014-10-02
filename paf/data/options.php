<?php

/**
* Options definitions
* 
* @package plugin-admin-framework
*/

/**
 * Defines options for this plugin
 */
function paf_options() {

	/**
	 * Add options here
	 */
	$options[ 'basic' ] = array(
		'page' => 'page_a',
	);
	$options[ 'basic_2' ] = array(
		'page' => 'page_a',
		'tab'  => 'tab_2',
	);
	$options[ 'basic_3' ] = array(
		'page' => 'page_b',
	);

	$options[ 'Text Area' ] = array(
		'type' => 'textarea',
		'editor' => 1,
		'page' => 'page_a',
	);

	$options[ 'Color Picker' ] = array(
		'page' => 'page_a',
	);

	$options[ 'advanced' ] = array(
		'title'         => __( 'Advanced' ),
		'subtitle'      => __( 'Belongs to page_a/tab_1' ),
		'type'          => 'textarea',
		'page'          => 'page_a',
		'tab'           => 'tab_1',
		'section'       => 'advanced',
		'section_title' => __( 'Advanced Stuff' ),
		'value'         => 'Some value',
	);

	/**
	 * Do not touch this line
	 */
	return empty( $options ) ? array() : $options;
}

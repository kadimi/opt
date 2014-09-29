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
	$options[ 'basic'] = array(
		'page' => 'page_a',
	);
	$options[ 'basic_2'] = array(
		'page' => 'page_b',
	);

	$options[ 'advanced'] = array(
		'type'          => 'textarea',
		'page'          => 'page_a',
		'tab'           => 'tab_1',
		'section'       => 'advanced',
		'section_title' => __( 'Advanced Stuff' ),
	);

	/**
	 * Do not touch this line
	 */
	return empty( $options ) ? array() : $options;
}

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

	$options[ 'my_colorpicker' ] = array(
		'page' => 'page_a',
		'colorpicker' => true,
	);

	$options[ 'my_upload' ] = array(
		'page' => 'page_a',
		'type' => 'upload',
	);

	$options[ 'my_dropdown' ] = array(
		'page' => 'page_a',
		'type' => 'select',
		'options' => array(
			'' => __( 'Choose an animal' ),
			'bird' => __( 'Bird' ),
			'cat' => __( 'Cat' ),
		),
		'selected' => 'bird',
		'multiple' => TRUE,
	);

	$options[ 'my_textarea' ] = array(
		'type' => 'textarea',
		'editor' => TRUE,
		'page' => 'page_a',
		'editor_height' => '120',
		'teeny' => TRUE,
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

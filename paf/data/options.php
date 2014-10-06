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
		'page'        => 'page_a',
		'colorpicker' => true,
	);

	$options[ 'my_upload' ] = array(
		'page' => 'page_a',
		'type' => 'upload',
	);

	$options[ 'my_dropdown_single' ] = array(
		'page'     => 'page_a',
		'type'     => 'select',
		'options'  => array(
			''     => __( 'Choose an animal' ),
			'bird' => __( 'Bird' ),
			'cat'  => __( 'Cat' ),
		),
		// 'selected' => array( 'bird', 'cat' ),
	);

	$options[ 'my_dropdown_multiple' ] = array(
		'page'     => 'page_a',
		'type'     => 'select',
		'options'  => array(
			'bird' => __( 'Bird' ),
			'cat'  => __( 'Cat' ),
		),
		'multiple' => TRUE,
		'selected' => array( 'bird', 'cat' ),
	);

	$options[ 'my_radios' ] = array(
		'page'    => 'page_a',
		'type'    => 'radio',
		'options' => array(
			'bird' => __( 'Bird' ),
			'cat'  => __( 'Cat' ),
		),
	);

	$options[ 'my_checkboxes' ] = array(
		'page'      => 'page_a',
		'type'      => 'checkbox',
		'options'   => array(
			'bird' => __( 'Bird' ),
			'cat'  => __( 'Cat' ),
		),
		'separator' => '&nbsp;&nbsp;|&nbsp;&nbsp;'
	);

	$options[ 'my_posts_checkboxes' ] = array(
		'page'    => 'page_a',
		'type'    => 'checkbox',
		'options' => 'posts',
	);

	$options[ 'my_posts_multi' ] = array(
		'page'     => 'page_a',
		'type'     => 'select',
		'options'  => 'posts',
		'multiple' => TRUE,
	);

	$options[ 'my_terms_dropdown' ] = array(
		'page'       => 'page_a',
		'type'       => 'radio',
		'options'    => 'terms',
		'taxonomies' => array( 'post_tag' ),
		'args'       => 'hide_empty=0',
	);

	$options[ 'my_textarea' ] = array(
		'type' => 'textarea',
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
		'editor'        => TRUE,
		'editor_height' => '120',
		'teeny'         => TRUE,
		'value'         => 'Some value',
	);

	/**
	 * Do not touch this line
	 */
	return empty( $options ) ? array() : $options;
}

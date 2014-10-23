<?php

/**
 * Options definitions
 * 
 * @package plugin-admin-framework
 */

// Make sure our temporary variable is empty
$options = array();

$options[ 'basic_0' ] = array(
	'title' => __( 'Basic text field' ),
	'subtitle' => __( 'id: <code>basic_0</code>' ),
	'page' => 'page_a',
	'description' => '~',
);

$options[ 'basic_1' ] = array(
	'page' => 'page_a',
	'placeholder' => __( 'Try &quot;123&quot;' ),
);

$options[ 'basic_2' ] = array(
	'page' => 'page_a',
	'tab'  => 'tab_2',
);

$options[ 'basic_3' ] = array( 'page' => 'page_b' );

$options[ 'basic_4' ] = array(
	'description' => '<p class="description">An option with  default value <code>DEF</code></p>',
	'page' => 'page_a',
	'default' => 'DEF',
);

$options[ 'my_dropdown_single' ] = array(
	'page'     => 'page_a',
	'type'     => 'select',
	'options'  => array(
		''     => __( 'Choose an animal' ),
		'bird' => __( 'Bird' ),
		'cat'  => __( 'Cat' ),
		'fish'  => __( 'Fish' ),
	),
	'description'     => '<p class="description">' . __( 'Try <strong>Bird</strong> or <strong>Cat</strong> here and <strong>123</strong> above to show a conditional field.' ) . '</p>',
	// 'selected' => array( 'bird', 'cat' ),
);

$options[ 'depenency_1' ] = array(
	'page' => 'page_a',
	'conditions' => array(
		array( 'basic_1', 'eq', '123' ),
	),
);

$options[ 'depenency_2' ] = array(
	'page' => 'page_a',
	'conditions' => array(
		array( 'basic_1', 'eq', '123' ),
		array( 'my_dropdown_single', 'in', 'cat,bird' ),
	),
	'description' => '~',
);

$options[ 'my_colorpicker' ] = array(
	'page'        => 'page_a',
	'colorpicker' => true,
);

$options[ 'my_upload' ] = array(
	'page' => 'page_a',
	'type' => 'upload',
);

$options[ 'my_dropdown_multiple' ] = array(
	'page'     => 'page_a',
	'type'     => 'select',
	'options'  => array(
		'bird' => __( 'Bird' ),
		'cat'  => __( 'Cat' ),
	),
	'multiple' => TRUE,
	// 'selected' => array( 'bird', 'cat' ),
);

$options[ 'my_radios' ] = array(
	'page'    => 'page_a',
	'type'    => 'radio',
	'options' => array(
		'bird' => __( 'Bird' ),
		'cat'  => __( 'Cat' ),
	),
);

$options[ 'my_radios_images' ] = array(
	'page'    => 'page_a',
	'type'    => 'checkbox',
	'options' => array(
		'pressapps' => 'http://placehold.it/120x40/35d/fff&text=pressapps',
		'wordpress' => 'http://placehold.it/120x40/3d5/fff&text=wordpress',
		'codecanyon' => 'http://placehold.it/120x40/d35/fff&text=codecanyon',
	),
	// 'selected' => array( 'pressapps', 'codecanyon' ),
);

$options[ 'my_radios_images_2' ] = array(
	'page'    => 'page_a',
	'type'    => 'checkbox',
	'options' => array(
		'pressapps' => 'http://placehold.it/120x40/35d/fff&text=pressapps',
		'wordpress' => 'http://placehold.it/120x40/3d5/fff&text=wordpress',
		'codecanyon' => 'http://placehold.it/120x40/d35/fff&text=codecanyon',
	),
	// 'selected' => array( 'wordpress' ),
	'separator' => ' ',
);

$options[ 'my_checkboxes' ] = array(
	'page'      => 'page_a',
	'type'      => 'checkbox',
	'options'   => array(
		'bird' => __( 'Bird' ),
		'cat'  => __( 'Cat' ),
	),
	'separator' => '&nbsp;&nbsp;|&nbsp;&nbsp;',
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
	'description'   => __( 'This is an advanced field' ),
	'editor'        => TRUE,
	'editor_height' => 200,
	// 'textarea_rows' => 10,
	'teeny'         => TRUE,
	'value'         => 'Overriden...Overriden...Overriden...',
);

// Register options
paf_options( $options );

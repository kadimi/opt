<?php

/**
 * @package plugin-admin-framework
 */

function paf_print_option( $option_id ) {
	
	global $paf_page_options;

	$option = $paf_page_options[ $option_id ];
	$option_type = K::get_var( 'type', $option, 'text' );

	// Determine the option callback function
	$callback = 'paf_print_option_type_' . $option_type;
	if( ! function_exists( $callback ) ) {
		$callback = 'paf_print_option_type_not_implemented';
	}

	call_user_func( $callback, array( $option_id => $option ) );
}

function paf_option_return_title( $option_def ) {

	$option_id = key( $option_def );
	$option = $option_def[ $option_id ];

	$title = K::get_var( 'title', $option, $option_id );
	$subtitle = K::get_var( 'subtitle', $option );

	$return = 
		$title 
		. ( $subtitle
			? '<br /><span style="font-weight: normal; font-style: italic; font-size: .9em;">' . $subtitle . '</span>'
			: '' 
		)
	;

	return $return;
}

function paf_print_option_type_text( $option_def ) {

	$option_id = key( $option_def );
	$option = $option_def[ $option_id ];

	K::input( 'paf_' . $option_id
		, array(
			'value' => K::get_var( 'value', $option, '' ),
		)
		, array(
			'colorpicker' => K::get_var( 'colorpicker', $option, FALSE ),
			'format' => sprintf( 
				'<table class="form-table"><tbody><tr><th scope="row">%s</th><td>:input<br />%s</td></tr></tbody></table>'
				, paf_option_return_title( $option_def )
				, ''//@d( $option )
			)
		)
	);
}

function paf_print_option_type_textarea( $option_def ) {

	$option_id = key( $option_def );
	$option = $option_def[ $option_id ];

	K::textarea( 'paf_' . $option_id
		, array(
			'class' => K::get_var( 'class', $option, 'large-text' ),
		)
		, array(
			'value' => K::get_var( 'value', $option, '' ),
			'editor' => K::get_var( 'editor', $option, FALSE ),
			'media_buttons' => K::get_var( 'media', $option, TRUE ),
			'format' => sprintf( 
				'<table class="form-table"><tbody><tr><th scope="row">%s</th><td>:textarea<br />%s</td></tr></tbody></table>'
				, paf_option_return_title( $option_def )
				, ''//@d( $option )
			)
		)
	);
}

function paf_print_option_type_select( $option_def ) {

	$option_id = key( $option_def );
	$option = $option_def[ $option_id ];

	K::select( 'paf_' . $option_id
		, array(
		)
		, array(
			'options' => K::get_var( 'options', $option, array() ),
			'selected' => K::get_var( 'selected', $option, '' ),
			'format' => sprintf( 
				'<table class="form-table"><tbody><tr><th scope="row">%s</th><td>:select<br />%s</td></tr></tbody></table>'
				, paf_option_return_title( $option_def )
				, ''//@d( $option )
			)
		)
	);
}

function paf_print_option_type_not_implemented( $option_def ) {

	$option_id = key( $option_def );
	$option = $option_def[ $option_id ];

	K::input( 'paf_' . $option_id
		, array(
			'value' => K::get_var( 'value', $option, '' ),
		)
		, array(
			'format' => sprintf( 
				'<table class="form-table"><tbody><tr><th scope="row">%s</th><td>:input<br />%s<br />%s</td></tr></tbody></table>'
				, paf_option_return_title( $option_def )
				, sprintf(
					'<p class="description"><span class="dashicons dashicons-no"></span> ' . __( 'The option type <code>%s</code> is not yet implemented' ) . '</p>'
					, $option[ 'type' ]
				)
				, ''//@d( $option )
			)
		)
	);
}
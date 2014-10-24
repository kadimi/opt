<?php

/**
 * @package plugin-admin-framework
 */

/**
 * Gets options
 * 
 * Return all paf options array
 * If $option_id is set, the function will that option current
 * If not find it will return it's default value if any
 * 
 * @param string $option_id
 * @return mixed The paf option value
 */
function paf( $option_id  = '' ) {

	$paf = get_option( 'paf', array() );

	if( strlen( $option_id ) ) {
		if( isset( $paf[ $option_id ] ) ) {
			return $paf[ $option_id ];
		} else {
			$def = paf_d( $option_id );
			return K::get_var( 'default', $def );
		}
	} else {
		return $paf;
	}
}

/**
 * Get option definition
 */
function paf_d( $option_id ){
	
	global $paf_options;
	
	return K::get_var( $option_id, $paf_options, FALSE );
}

function paf_print_option( $option_id ) {
	
	global $paf_page_options;

	$option = $paf_page_options[ $option_id ];
	$option_type = K::get_var( 'type', $option, 'text' );

	// Determine the option callback function
	$callback = 'paf_print_option_type_' . $option_type;
	if( ! function_exists( $callback ) ) {
		$callback = 'paf_print_option_type_not_implemented';
	}

	// Sort option parameters for a better display when using 'description' = '~'
	ksort( $option );

	/**
	 * Call the function coresponding to the option, 
	 * e.g. paf_print_option_type_text or paf_print_option_type_upload
	 */
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

function paf_option_return_format( $option_type = 'input' ) {

	switch ( $option_type ) {
		case 'upload':
			return '<table class="form-table"><tbody><tr><th scope="row">%s</th><td>:input%s</td></tr></tbody></table>';
		case 'input':
		case 'select':
		case 'textarea':
		default:
			return '<table class="form-table"><tbody><tr><th scope="row">%s</th><td>:' . $option_type . '<br />%s</td></tr></tbody></table>' ;
	}
}

/**
  * Generate formatted and syntax highlighted dump
  */
function paf_option_return_dump( $option_id ) {

	global $paf_options;
	$option = $paf_options[ $option_id ];
	ksort( $option );

	array_walk_recursive( $option, 'paf_htmlspecialchars_recursive' );

	$dump = K::wrap(
			"\$options[ '$option_id' ] = "
			. var_export( $option, true )
			. ';'
		, array(
			'class' => 'php paf-code-block',
		)
		, array(
			'html_before' => '<pre>',
			'html_after' => '</pre>',
			'in' => 'code',
			'return' => true,
		)
	);

	// Remove white space before 'array ('
	$dump = preg_replace( '/=>\s+array \(/s', '=> array (', $dump );

	// Replace 2 spaces with 4 spaces
	$pattern = "/((?:  )+)(\d+|'|array|\))/";
	$dump = preg_replace( $pattern, '\1$1$2', $dump );

	return $dump;
}

function paf_print_option_type_text( $option_def ) {

	$option_id = key( $option_def );
	$option = $option_def[ $option_id ];

	if( '~' === K::get_var( 'description', $option ) ) {
		$option[ 'description' ] = paf_option_return_dump( $option_id );
	}

	K::input( 'paf[' . $option_id . ']'
		, array(
			'class' => 'regular-text',
			'placeholder' => K::get_var( 'placeholder', $option ),
			'value' => isset( $option[ 'value' ] )
				? $option[ 'value' ]
				: paf( $option_id )
			,
			'data-paf-conditions' => K::get_var( 'conditions', $option )
				? urlencode( json_encode( K::get_var( 'conditions', $option ), JSON_FORCE_OBJECT ) )
				: null
			,
			'data-paf-default' => K::get_var( 'default', $option ),
		)
		, array(
			'colorpicker' => K::get_var( 'colorpicker', $option, FALSE ),
			'format' => sprintf( 
				paf_option_return_format()
				, paf_option_return_title( $option_def )
				, K::get_var( 'description', $option, '' )
			)
		)
	);
}

function paf_print_option_type_textarea( $option_def ) {

	$option_id = key( $option_def );
	$option = $option_def[ $option_id ];

	if( '~' === K::get_var( 'description', $option ) ) {
		$option[ 'description' ] = paf_option_return_dump( $option_id );
	}

	K::textarea( 'paf[' . $option_id . ']'
		, array(
			'class' => K::get_var( 'class', $option, 'large-text' ),
			'data-paf-conditions' => K::get_var( 'conditions', $option )
				? urlencode( json_encode( K::get_var( 'conditions', $option ), JSON_FORCE_OBJECT ) )
				: null
			,
			'data-paf-default' => K::get_var( 'default', $option ),
		)
		, array(
			'value' => isset( $option[ 'value' ] )
				? $option[ 'value' ]
				: paf( $option_id )
			,
			'editor' => K::get_var( 'editor', $option, FALSE ),
			'editor_height' => K::get_var( 'editor_height', $option ),
			'format' => sprintf( 
				paf_option_return_format( 'textarea' )
				, paf_option_return_title( $option_def )
				, K::get_var( 'description', $option, '' )
			),
			'media_buttons' => K::get_var( 'media_buttons', $option, TRUE ),
			'teeny' => K::get_var( 'teeny', $option ),
			'textarea_rows' => K::get_var( 'textarea_rows', $option, 20 ),
		)
	);
}

function paf_print_option_type_select( $option_def ) {

	$option_id = key( $option_def );
	$option = $option_def[ $option_id ];

	if( '~' === K::get_var( 'description', $option ) ) {
		$option[ 'description' ] = paf_option_return_dump( $option_id );
	}

	$is_radio = 'radio' === $option[ 'type'];
	$is_checkbox = 'checkbox' === $option[ 'type'];
	$is_multiple = $is_checkbox || K::get_var( 'multiple', $option );

	// Enqueue select 2
	if( ! $is_checkbox && ! $is_radio ) {
		$protocol = is_ssl() ? 'https' : 'http';
		wp_enqueue_script( 'select2', $protocol . '://cdnjs.cloudflare.com/ajax/libs/select2/3.5.0/select2.js' );
		wp_enqueue_style( 'select2', $protocol . '://cdnjs.cloudflare.com/ajax/libs/select2/3.5.0/select2.min.css' );
	}

	$options = array();
	switch ( K::get_var( 'options', $option, array() ) ) {
		case 'posts':
			$posts = query_posts( K::get_var( 'args', $option, '' ) );
			foreach ( $posts as $post ) {
				$options[ $post->ID ] = $post->post_title;
			}
			break;
		case 'terms':
			$terms = get_terms(
				K::get_var( 'taxonomies', $option, '' )
				, K::get_var( 'args', $option )
			);
			foreach ( $terms as $term ) {
				$options[ $term->term_id ] = $term->name;
			}
			break;
		default:
			$options = K::get_var( 'options', $option, array() );
			break;
	}

	// Add an empty option to prevent auto-selecting the first radio
	if( $is_radio ) {
		$options = array( '__none__' => '' ) + $options;
	}

	K::select( 'paf[' . $option_id . ']'
		, array(
			'class' => 'paf-option-type-' . $option[ 'type' ],
			'data-paf-separator' => K::get_var( 'separator', $option, '<br />' ),
			'multiple' => $is_multiple,
			'style' => 'min-width: 25em;',
			'data-paf-conditions' => K::get_var( 'conditions', $option )
				? urlencode( json_encode( K::get_var( 'conditions', $option ), JSON_FORCE_OBJECT ) )
				: null
			,
		)
		, array(
			'options' => $options,
			'selected' => isset( $option[ 'selected' ] )
				? $option[ 'selected' ]
				: paf( $option_id )
			,
			'format' => sprintf( 
				paf_option_return_format( 'select' )
				, paf_option_return_title( $option_def )
				, K::get_var( 'description', $option, '' )
			)
		)
	);
}

function paf_print_option_type_radio( $option_def ) {

	return paf_print_option_type_select( $option_def );
}

function paf_print_option_type_checkbox( $option_def ) {	

	return paf_print_option_type_select( $option_def );
}

function paf_print_option_type_upload( $option_def ) {

	$option_id = key( $option_def );
	$option = $option_def[ $option_id ];

	if( '~' === K::get_var( 'description', $option ) ) {
		$option[ 'description' ] = paf_option_return_dump( $option_id );
	}

	$option_html_name = 'paf[' . $option_id . ']';

	// Output
	K::input( 'paf[' . $option_id . ']'
		, array(
			'class' => 'paf-option-type-upload regular-text',
			'value' => isset( $option[ 'value' ] )
				? $option[ 'value' ]
				: paf( $option_id )
			,
			'data-paf-conditions' => K::get_var( 'conditions', $option )
				? urlencode( json_encode( K::get_var( 'conditions', $option ), JSON_FORCE_OBJECT ) )
				: null
			,
			'data-paf-default' => K::get_var( 'default', $option ),
		)
		, array(
			'format' => sprintf( 
				paf_option_return_format( 'upload' )
				, paf_option_return_title( $option_def )
				, '<a class="button">' . __( 'Select Media') . '</a>'
				, K::get_var( 'description', $option, '' )
			)
		)
	);
}

function paf_print_option_type_not_implemented( $option_def ) {

	$option_id = key( $option_def );
	$option = $option_def[ $option_id ];

	if( '~' === K::get_var( 'description', $option ) ) {
		$option[ 'description' ] = paf_option_return_dump( $option_id );
	}

	K::input( 'paf[' . $option_id . ']'
		, array(
			'value' => K::get_var( 'value', $option, '' ),
		)
		, array(
			'format' => sprintf( 
				paf_option_return_format()
				, paf_option_return_title( $option_def )
				, sprintf(
					'<p class="description"><span class="dashicons dashicons-no"></span> ' . __( 'The option type <code>%s</code> is not yet implemented' ) . '</p>'
					, $option[ 'type' ]
				)
				, K::get_var( 'description', $option, '' )
			)
		)
	);
}

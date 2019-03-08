<?php

/**
 * @package opt
 */

/**
 * Gets options
 * 
 * Return all opt options array
 * If $option_id is set, the function will that option current value
 * If not find it will return it's default value if it's defined
 * 
 * @param string $option_id
 * @return mixed The opt option value
 */
function opt( $option_id  = '' ) {

	$opt = get_option( 'opt', array() );

	if( strlen( $option_id ) ) {
		if( isset( $opt[ $option_id ] ) ) {
			return $opt[ $option_id ];
		} else {
			$def = opt_d( $option_id );
			return K::get_var( 'default', $def );
		}
	} else {
		return $opt;
	}
}

/**
 * Get option definition
 */
function opt_d( $option_id ){
	
	global $opt_options;
	
	return K::get_var( $option_id, $opt_options, FALSE );
}

function opt_print_option( $option_id, $alt = array() ) {
	
	if ( $alt ) {
		$option = $alt;
	} else {
		global $opt_page_options;
		$option = $opt_page_options[ $option_id ];
	}

	$option = opt_option_prepare( $option, $option_id );
	$option_type = K::get_var( 'type', $option, 'text' );

	// Determine the option callback function
	$callback = 'opt_print_option_type_' . $option_type;
	if( ! function_exists( $callback ) ) {
		$callback = 'opt_print_option_type_not_implemented';
	}

	// Sort option parameters for a better display when using 'description' = '~'
	ksort( $option );

	/**
	 * Call the function coresponding to the option, 
	 * e.g. opt_print_option_type_text or opt_print_option_type_media
	 */
	call_user_func( $callback, array( $option_id => $option ) );
}

function opt_option_return_title( $option_def ) {

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

function opt_option_return_format( $option_type = 'input' ) {

	switch ( $option_type ) {
		case 'media':
			return '<table class="form-table"><tbody><tr><th scope="row">%s</th><td>:input%s<br />%s</td></tr></tbody></table>';
		case 'input':
		case 'select':
		case 'textarea':
		default:
			return '<table class="form-table"><tbody><tr><th scope="row">%s</th><td>:' . $option_type . '<br />%s</td></tr></tbody></table>' ;
	}
}

/**
 * Prepare option
 * 
 * Change different option attributes to a suitable format
 */
function opt_option_prepare( $option, $option_id = null ) {

	// Add type if not specified
	if( ! isset( $option[ 'type' ] ) ) {
		$option[ 'type' ] = 'text';
	}

	// Format selected as an array
	if( isset( $option[ 'selected' ] ) ) {
		$option[ 'selected' ] = K::get_var( 'selected', $option, array() );
		if ( ! is_array( $option[ 'selected' ] ) ) {
			$option[ 'selected' ] = explode( ',', $option[ 'selected' ] );
		}
	}

	// Format default as an array
	if( in_array( $option[ 'type' ], array( 'select', 'radio', 'checkbox' ) ) ) {
		if( isset( $option[ 'default' ] ) ) {
			$option[ 'default' ] = K::get_var( 'default', $option, array() );
			if ( ! is_array( $option[ 'default' ] ) ) {
				$option[ 'default' ] = explode( ',', $option[ 'default' ] );
			}
		}
	}

	// Add code for generating the option is the description is "~"
	if( $option_id ) {
		if( '~' === K::get_var( 'description', $option ) ) {
			$option[ 'description' ] = opt_option_return_dump( $option_id );
		}
	}

	return $option;
}

/**
  * Generate formatted and syntax highlighted dump
  */
function opt_option_return_dump( $option_id ) {

	global $opt_options;
	$option = $opt_options[ $option_id ];
	ksort( $option );

	array_walk_recursive( $option, 'opt_htmlspecialchars_recursive' );

	$dump = K::wrap(
			"\$options[ '$option_id' ] = "
			. var_export( $option, true )
			. ';'
		, array(
			'class' => 'php opt-code-block',
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
	$dump = preg_replace( $pattern, '\1\1\2', $dump );

	return $dump;
}

function opt_print_option_type_text( $option_def ) {

	$option_id = key( $option_def );
	$option = $option_def[ $option_id ];

	K::input( 'opt[' . $option_id . ']'
		, array(
			'class' => 'regular-text',
			'placeholder' => K::get_var( 'placeholder', $option ),
			'value' => isset( $option[ 'value' ] )
				? $option[ 'value' ]
				: opt( $option_id )
			,
			'data-opt-conditions' => K::get_var( 'conditions', $option )
				? urlencode( json_encode( K::get_var( 'conditions', $option ), JSON_FORCE_OBJECT ) )
				: null
			,
			'data-opt-default' => K::get_var( 'default', $option ),
		)
		, array(
			'colorpicker' => K::get_var( 'colorpicker', $option, FALSE ),
			'format' => sprintf( 
				opt_option_return_format()
				, opt_option_return_title( $option_def )
				, K::get_var( 'description', $option, '' )
			)
		)
	);
}

function opt_print_option_type_textarea( $option_def ) {

	$option_id = key( $option_def );
	$option = $option_def[ $option_id ];

	if( '~' === K::get_var( 'description', $option ) ) {
		$option[ 'description' ] = opt_option_return_dump( $option_id );
	}

	$style = '';
	foreach( array( 'height', 'width' ) as $prop ) {
		$style .= K::get_var( $prop, $option )
			? sprintf( '%:%;', $prop, $option[ $prop ] )
			: ''
		;
	}

	K::textarea( 'opt[' . $option_id . ']'
		, array(
			'class' => K::get_var( 'cols', $option ) ? '' : 'large-text',
			'cols' => K::get_var( 'cols', $option ),
			'rows' => K::get_var( 'rows', $option ),
			'style' => $style,
			'data-opt-conditions' => K::get_var( 'conditions', $option )
				? urlencode( json_encode( K::get_var( 'conditions', $option ), JSON_FORCE_OBJECT ) )
				: null
			,
			'data-opt-default' => K::get_var( 'default', $option ),
		)
		, array(
			'value' => isset( $option[ 'value' ] )
				? $option[ 'value' ]
				: opt( $option_id )
			,
			'editor' => K::get_var( 'editor', $option, FALSE ),
			'editor_height' => K::get_var( 'editor_height', $option ),
			'format' => sprintf( 
				opt_option_return_format( 'textarea' )
				, opt_option_return_title( $option_def )
				, K::get_var( 'description', $option, '' )
			),
			'media_buttons' => K::get_var( 'media_buttons', $option, TRUE ),
			'teeny' => K::get_var( 'teeny', $option ),
			'textarea_rows' => K::get_var( 'textarea_rows', $option, 20 ),
		)
	);
}

function opt_print_option_type_select( $option_def ) {

	$option_id = key( $option_def );
	$option = $option_def[ $option_id ];

	if( '~' === K::get_var( 'description', $option ) ) {
		$option[ 'description' ] = opt_option_return_dump( $option_id );
	}

	$is_radio = 'radio' === $option[ 'type' ];
	$is_checkbox = 'checkbox' === $option[ 'type' ];
	$is_select = 'select' === $option[ 'type' ];
	$is_multiple = $is_checkbox || K::get_var( 'multiple', $option );

	// Enqueue select 2
	if( ! $is_checkbox && ! $is_radio ) {
		$protocol = is_ssl() ? 'https' : 'http';
		wp_enqueue_script( 'select2', $protocol . '://cdn.jsdelivr.net/npm/select2@4.0.6-rc.1/dist/js/select2.min.js' );
		wp_enqueue_style( 'select2', $protocol . '://cdn.jsdelivr.net/npm/select2@4.0.6-rc.1/dist/css/select2.min.css' );
	}

	$options = array();
	switch ( K::get_var( 'options', $option, array() ) ) {
		case 'posts':
			$posts = query_posts( K::get_var( 'args', $option, '' ) );
			foreach ( $posts as $post ) {
				$options[ $post->ID ] = $post->post_title;
			}
			if ( $is_select && ! $is_multiple ) {
				$options = array( '' ) + $options;
			}
			break;
		case 'terms':
			$taxonomies = K::get_var( 'taxonomies', $option, 'category,post_tag,link_category,post_format' );
			if ( ! is_array( $taxonomies ) ) {
				$taxonomies = explode( ',', $taxonomies );
			}
			$args = K::get_var( 'args', $option, array() );
			$terms = get_terms( $taxonomies, $args );
			foreach ( $terms as $term ) {
				$options[ $term->term_id ] = $term->name;
			}
			if ( $is_select && ! $is_multiple ) {
				$options = array( '' ) + $options;
			}
			break;
		case 'sites':
			$options = array();
			global $wpdb;
			$sites_tbl = $wpdb->base_prefix . "blogs";
			$sites = $wpdb->get_results( "SELECT blog_id FROM {$sites_tbl} ORDER BY blog_id" );
			foreach( $sites as $site ) {
				$siteinfo = get_blog_details($site->blog_id);
				$options[$site->blog_id] = $siteinfo->blogname;
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

	// Escape HTML
	foreach ( $options as $k => $v ) {
		$options[ $k ] = htmlspecialchars( $v );
	}

	K::select( 'opt[' . $option_id . ']'
		, array(
			'class' => 'opt-option-type-' . $option[ 'type' ],
			'data-opt-separator' => K::get_var( 'separator', $option, '<br />' ),
			'multiple' => $is_multiple,
			'style' => 'min-width: 25em;',
			'data-opt-conditions' => K::get_var( 'conditions', $option )
				? urlencode( json_encode( K::get_var( 'conditions', $option ), JSON_FORCE_OBJECT ) )
				: null
			,
			'data-opt-default' => K::get_var( 'default', $option )
				? urlencode( json_encode( K::get_var( 'default', $option ), JSON_FORCE_OBJECT ) )
				: null
			,
		)
		, array(
			'options' => $options,
			'selected' => isset( $option[ 'selected' ] )
				? $option[ 'selected' ]
				: opt( $option_id )
			,
			'format' => sprintf( 
				opt_option_return_format( 'select' )
				, opt_option_return_title( $option_def )
				, K::get_var( 'description', $option, '' )
			)
		)
	);
}

function opt_print_option_type_radio( $option_def ) {

	return opt_print_option_type_select( $option_def );
}

function opt_print_option_type_checkbox( $option_def ) {	

	return opt_print_option_type_select( $option_def );
}

function opt_print_option_type_media( $option_def ) {

	$option_id = key( $option_def );
	$option = $option_def[ $option_id ];

	if( '~' === K::get_var( 'description', $option ) ) {
		$option[ 'description' ] = opt_option_return_dump( $option_id );
	}

	$button_text = K::get_var( 'button_text', $option, __( 'Select media' ) );

	// Output
	K::input( 'opt[' . $option_id . ']'
		, array(
			'class' => 'opt-option-type-media regular-text',
			'placeholder' => K::get_var( 'placeholder', $option ),
			'value' => isset( $option[ 'value' ] )
				? $option[ 'value' ]
				: opt( $option_id )
			,
			'data-opt-conditions' => K::get_var( 'conditions', $option )
				? urlencode( json_encode( K::get_var( 'conditions', $option ), JSON_FORCE_OBJECT ) )
				: null
			,
			'data-opt-default' => K::get_var( 'default', $option ),
		)
		, array(
			'format' => sprintf( 
				opt_option_return_format( 'media' )
				, opt_option_return_title( $option_def )
				, '<a class="button">' . $button_text . '</a>'
				, K::get_var( 'description', $option, '' )
			)
		)
	);
}

function opt_print_option_type_not_implemented( $option_def ) {

	$option_id = key( $option_def );
	$option = $option_def[ $option_id ];

	if( '~' === K::get_var( 'description', $option ) ) {
		$option[ 'description' ] = opt_option_return_dump( $option_id );
	}

	K::input( 'opt[' . $option_id . ']'
		, array(
			'value' => K::get_var( 'value', $option, '' ),
		)
		, array(
			'format' => sprintf( 
				opt_option_return_format()
				, opt_option_return_title( $option_def )
				, sprintf(
					'<p class="description"><span class="dashicons dashicons-no"></span> ' . __( 'The option type <code>%s</code> is not yet implemented' ) . '</p>'
					, $option[ 'type' ]
				)
				, K::get_var( 'description', $option, '' )
			)
		)
	);
}

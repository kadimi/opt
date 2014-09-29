<?php

/**
 * @package plugin-admin-framework
 */

function paf_print_option( $option_id ) {
	
	global $paf_page_options;

	$option = $paf_page_options[ $option_id ];


	switch ( K::get_var( 'type', $option ) ) {
		default:
			$callback = 'text';
	}

	call_user_func( 'paf_print_option_' . $callback, $option_id, $option );
}

function paf_print_option_text( $option_id, $option ) {

	K::input( 'paf_' . $option_id
		, null
		, array(
			'format' => sprintf( 
				'<table class="form-table"><tbody><tr><th scope="row">%s</th><td>:input<br />%s</td></tr></tbody></table>'
				, $option_id
				, @d( $option )
			)
		)
	);
}


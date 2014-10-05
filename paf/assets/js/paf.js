jQuery( document ).ready( function( $ ) {

	// Media upload fields
	$( '.paf-option-type-upload' ).each( function() {
		var $input = $( this );
		var $button = $input.siblings( 'a' );

		$input.add( $button ).click( function() {

			wp.media.editor.send.attachment = function( props, attachment ) {
				$input.val( attachment.url ).change();
			}
			wp.media.editor.open( $ );
			return false;
		} );
	} );

	// turn select.paf-(radio|checkbox) into radio|checkbox
	$( '.paf-option-type-checkbox,.paf-option-type-radio' ).each( function( i, select ) {
		
		var $this = $( this );
		var $select = $( select );
		var type = $this.hasClass( 'paf-option-type-checkbox' ) ? 'checkbox' : 'radio';
		
		$select.find( 'option' ).each( function( j, option ){

			var $option = $( option );

			// Create a choice
			var $choice = $( '<input />' )
				.attr( 'type', type )
				.attr( 'name', $select.attr( 'name' ) )
				.attr( 'value', $option.val () )
			;

			// Set checked if the option was selected
			if ( $option.attr( 'selected' ) ) {
				$choice.attr( 'checked', 'checked' );
			}

			// Wrap inside label
			$choice = $( '<label />' ).html( $choice[0].outerHTML + $option.text() );

			// Insert 
			$select.before( $choice );
			$select.before("<br />");
		});

		// Remove dropdown
		$select.remove();
	} );

} );
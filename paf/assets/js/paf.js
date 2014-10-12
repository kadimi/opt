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

	// Add thumbnails to media upload fields
	$( '.paf-option-type-upload' ).change( function() {

		var $input = $( this );
		var $button = $input.siblings( 'a.button' );
		var $parent = $input.parent();
		var value = $input.val();
		var preview_url;
		var $element;

		// Add a thumbnail placeholder if missing
		$element = $parent.find( '> .paf-option-type-upload-preview' );
		if( ! $element.length ) {
			$parent.append( '<div class="paf-option-type-upload-preview"></div>');
			$element = $parent.find( '> .paf-option-type-upload-preview' );
		}

		// Empty then fill the placeholder
		$element.html( null );
		if ( value ) {
			if ( -1 !== $.inArray( extension( value ), [ 'gif', 'jpeg', 'jpg', 'png' ] ) ) {
				// File is an image
				preview_url = value;
			} else {
				// File is not an image
				preview_url = paf_assets + 'img/' + extension( value ) + '.png';
			}

			var $img = $( '<img />' )
				.attr( 'src', preview_url )
				.attr( 'alt', value )
				.attr( 'title', value )
			;
			var $a = $( '<a />' )
				.attr( 'class', 'paf-media-delete-button' )
				.attr( 'href', '#' )
				.append( '<div class="media-modal-icon">Remove</div>' )
			;
			$element.append( $img );
			$element.append( $a );
			$()
				.add( $input )
				.add( $button )
				.hide()
			;
		} else {
			$()
				.add( $input )
				.add( $button )
				.slideDown( 'fast' )
			;
		}
	} ).change();

	// Handle media removal buttons
	$( document ).on( 'click', '.paf-media-delete-button', function( e ) {
		
		e.preventDefault();

		var $delete_button = $( this );
		var $input = $delete_button.parent().siblings( 'input' );
		
		$input.val( '' ).change();
	} );

	// Select2
	if ( $.isFunction( $.fn.select2 ) ) {

		$( '.paf-option-type-select' ).select2();
	}

	// turn select.paf-(radio|checkbox) into radio|checkbox
	$( '.paf-option-type-checkbox,.paf-option-type-radio' ).each( function( i, select ) {

		var $this = $( this );
		var $select = $( select );
		var type = $this.hasClass( 'paf-option-type-checkbox' ) ? 'checkbox' : 'radio';
		var separator = $this.data( 'paf-separator' );

		$select.find( 'option' ).each( function( j, option ){

			var $option = $( option );
			var option_is_url = isURL( $option.text() );

			// Turn URLs into image tags
			if ( option_is_url ) {
				$option.text( '<img src="' + $option.text() + '" />' );
			}

			// Skip the empty radio
			if( 'radio' === type && '__none__' === $option.val() ) {
				return;
			}

			// Create a choice
			var $choice = $( '<input />' )
				.attr( 'type', type )
				.attr( 'name', $select.attr( 'name' ) )
				.attr( 'value', $option.val() )
			;

			// Set checked if the option was selected
			if ( $option.attr( 'selected' ) ) {
				$choice.attr( 'checked', 'checked' );
			}

			// Wrap inside label
			$choice = $( '<label />' ).html( $choice[0].outerHTML + $option.text() );

			// Mark if image 
			if ( option_is_url ) {
				$choice.addClass( 'paf-label-with-image' );
			}

			// Insert 
			$select.before( $choice );

			// Insert separator after all but last option
			if( j < $select.find( 'option' ).length - 1 ) {
				$select.before( separator );
			}
		} );

		// Remove dropdown
		$select.remove();
	} );

	// Show/hide form on load/unload
	$( '#paf-form' ).show();
	$( window ).on( 'beforeunload', function() {

		$( '#paf-form' ).animate( { opacity: '0' }, 100 );
	} );

	function isURL( url ) {

		var pattern = new RegExp('^(https?:\\/\\/)?'				// protocol
			+ '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'	// domain name
			+ '((\\d{1,3}\\.){3}\\d{1,3}))'							// OR ip (v4) address
			+ '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'						// port and path
			+ '((\\?|&)[;&a-z\\d%_.~+=-]*)?'						// query string
			+ '(\\#[-a-z\\d_]*)?$','i'								// fragment locator
		);
		if( pattern.test( url ) ) {
			return true;
		} else {
			return false;
		}
	}

	function extension( path ) {

		return path.split('.').pop();
	}
} );

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

	// turn select.paf-radio into radio button
	$( '_todo_select' ).each( function( i, select ) {
		var $select = $(select);
		$select.find('option').each(function(j, option){
			var $option = $(option);
			// Create a radio:
			var $radio = $('<input type="radio" />');
			// Set name and value:
			$radio.attr('name', $select.attr('name')).attr('value', $option.val());
			// Set checked if the option was selected
			if ($option.attr('selected')) $radio.attr('checked', 'checked');
			// Insert radio before select box:
			$select.before($radio);
			// Insert a label:
			$select.before(
			  $("<label />").attr('for', $select.attr('name')).text($option.text())
			);
			// Insert a <br />:
			$select.before("<br/>");
		});
		$select.remove();
	});

} );
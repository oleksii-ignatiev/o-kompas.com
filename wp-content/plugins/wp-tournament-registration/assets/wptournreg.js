jQuery(function() {
  jQuery(".wptournreg-list table").tablesorter({widgets: ["zebra"],});
});

/* Change selected player in editor. */
jQuery( '.wptournregedit-select' ).on( 'change', function() {

	jQuery( '.wptournregedit-participant' ).css( 'display', 'none' );
	jQuery( jQuery( this ).val() ).css( 'display', 'block' );
	
}).trigger( 'change' );

/* Approve player. */
jQuery( '.wptournregedit-participant').each( function() {
	
	let form = jQuery( this );
	let select = jQuery( '.wptournregedit-select>[value="#wptournregedit-participant' + form.find( '[name=id]' ).val() + '"]' );
	form.find( '[name=approved]' ).on( 'change', function() {
		
		if ( jQuery( this ).is( ':checked' ) ) {
			
			select.removeClass( 'wptournregedit-not-approved' );
		}
		else {
			
			select.addClass( 'wptournregedit-not-approved' );
		}
	});
});

/* Check for bots */
var wptournregtouched = false;
jQuery( '.wptournreg-form input' ).on( 'focus mouseover touch', function() {
	
	if ( wptournregtouched === false ) {
		
		wptournregtouched = true;
		jQuery( '.wptournreg-form' ).prepend( '<input type="hidden" name="touched" value="1">' );
	}
});
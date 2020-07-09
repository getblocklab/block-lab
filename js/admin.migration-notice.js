/* global XMLHttpRequest, FormData, ajaxurl */

document.addEventListener( 'DOMContentLoaded', function() {
	const hiddenClass = 'bl-hidden';

	// In the main migration notice, on clicking 'Not Now',
	// make an AJAX request to store the user meta to not display the notice again.
	// Also, remove this notice and display another.
	document.querySelector( '#bl-notice-not-now' ).addEventListener( 'click', function() {
		const request = new XMLHttpRequest();
		const data = new FormData();
		data.append( 'action', 'bl_dismiss_migration_notice' );
		data.append( 'bl-migration-nonce-name', document.querySelector( '#bl-migration-nonce-name' ).value );

		request.open( 'POST', ajaxurl, true );
		request.send( data );

		// Remove this notice.
		const notice = document.querySelector( '#bl-migration-notice' );
		notice.parentNode.removeChild( notice );

		// Display the 'Not Now' notice.
		document.querySelector( '#bl-not-now-notice' ).classList.remove( hiddenClass );
	} );

	// In the 'Not Now' notice, on clicking 'OK', hide the notice.
	document.querySelector( '#bl-notice-ok' ).addEventListener( 'click', function() {
		document.querySelector( '#bl-not-now-notice' ).classList.add( hiddenClass );
	} );
} );

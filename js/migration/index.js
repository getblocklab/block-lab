/**
 * WordPress dependencies
 */
import domReady from '@wordpress/dom-ready';
import { render } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { App } from './components';

// Renders the app in the container.
domReady( () => {
	render(
		<App />,
		document.querySelector( '.bl-migration__content' )
	);
} );

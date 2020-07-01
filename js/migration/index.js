/**
 * WordPress dependencies
 */
import domReady from '@wordpress/dom-ready';
import { render } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { App } from './components';

// Finds the block containers, and render the React component in them.
domReady( () => {
	render( <App />, document.querySelector( '.bl-migration__content' ) );
} );

/**
 * WordPress dependencies
 */
const { domReady } = wp;
const { dispatch } = wp.data;
const { render } = wp.element;

/**
 * Internal dependencies
 */
import { Editor } from './components';

domReady( () => {
	dispatch( 'core/editor' ).updateEditorSettings( { richEditingEnabled: false } );
	render(
		<Editor />,
		document.getElementById( 'bl-block-editor' ),
	);
} );

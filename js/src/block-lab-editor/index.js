/**
 * WordPress dependencies
 */
const { domReady } = wp;
const { dispatch } = wp.data;
const { render } = wp.element;

/**
 * Internal dependencies
 */
import { BlockLabEditor } from './components';

domReady( () => {
	dispatch( 'core/editor' ).updateEditorSettings( { richEditingEnabled: false } );
	render(
		<BlockLabEditor />,
		document.getElementById( 'bl-block-editor' ),
	);
} );

/**
 * External dependencies
 */
import '@testing-library/jest-dom/extend-expect';
import { fireEvent, getByText, render, waitFor } from '@testing-library/react';

/**
 * Internal dependencies
 */
import BlockEditor from '../helpers/block-editor';
import { registerBlocks } from '../../../../js/blocks/helpers';
import { Edit } from '../../../../js/blocks/components';

const blockSlug = 'test-text';
const blockName = `block-lab/${ blockSlug }`;
const blockTitle = 'Test Text';
const help = 'Here is some help text';
const defaultValue = 'This is the default value';
const placeholder = 'This is a placeholder';
const blockLabBlocks = {
	'block-lab/test-text': {
		name: blockName,
		title: blockTitle,
		excluded: [],
		icon: 'block_lab',
		category: {
			slug: 'common',
			title: 'Common Blocks',
			icon: null,
		},
		keywords: [ '' ],
		fields: {
			text: {
				name: 'text',
				label: 'Text',
				control: 'text',
				type: 'string',
				order: 0,
				location: 'editor',
				width: '100',
				maxlength: null,
				default: defaultValue,
				help,
				placeholder,
			},
		},
	},
};
const blockLab = {
	authorBlocks: [ blockSlug ],
	postType: 'post',
};

/**
 * Whether the node has the text in its textContent.
 *
 * @param {Object} nodeToSearch The element in which to search for the text.
 * @param {string} text The text to search the node for.
 */
const hasText = ( nodeToSearch, text ) => -1 !== nodeToSearch.textContent.indexOf( text );

describe( 'TextBlock', () => {
	it( 'displays the block in the inserter and the block has the expected values when added', () => {
		const { getByLabelText, getAllByPlaceholderText } = render(
			<BlockEditor blockRegistration={ () => registerBlocks( blockLab, blockLabBlocks, Edit ) } />
		);
		const button = document.querySelector( '.editor-inserter__toggle' );

		// Click the inserter button to see the available blocks.
		fireEvent.click( button );
		const searchInput = getByLabelText( 'Search for a block' );

		// Enter the name of the tested block.
		fireEvent.change( searchInput, { target: { value: blockTitle } } );
		const blockResults = document.querySelector( '.block-editor-inserter__results' );

		// The tested block should appear in the available blocks.
		expect( hasText( blockResults, blockTitle ) ).toStrictEqual( true );
		const blockButton = getByText( blockResults, blockTitle );

		// Click the tested block, to add it to the editor.
		fireEvent.click( blockButton );
		const blockEdit = document.querySelector( '.editor-block-list__block-edit' );
		const textInput = blockEdit.querySelector( 'input' );

		// The block should have the values from blockLabBlocks.
		expect( hasText( blockEdit, help ) ).toStrictEqual( true );
		expect( getAllByPlaceholderText( placeholder ) ).toHaveLength( 1 );
		expect( textInput.value ).toStrictEqual( defaultValue );

		const enteredText = 'This is some entered text';
		fireEvent.input( textInput, { target: { value: enteredText } } );

		// Click inside and outside the block.
		fireEvent.click( textInput );
		fireEvent.click( document.querySelector( '.editor-default-block-appender__content' ) );

		// The <ServerSideRender> should now display, instead of the block's field.
		waitFor( () => expect( document.querySelector( '.block-lab-editor__ssr' ) ).toBeInTheDocument() );

		// Click the block again.
		fireEvent.click( blockEdit );

		// The text entered in the <input> should still be there.
		expect( textInput.value ).toStrictEqual( enteredText );
	} );
} );

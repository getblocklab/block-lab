/**
 * External dependencies
 */
import '@testing-library/jest-dom/extend-expect';
import { fireEvent, getByText, render, waitFor } from '@testing-library/react';

/**
 * Internal dependencies
 */
import { BlockEditor, hasText } from '../helpers';
import { registerBlocks } from '../../../../js/blocks/helpers';
import { Edit } from '../../../../js/blocks/components';

const blockSlug = 'test-repeater';
const blockName = `block-lab/${ blockSlug }`;
const blockTitle = 'Test Repeater';
const help = 'Here is some help text';
const minSubFields = 1;
const maxSubFields = 3;

const textarea = {
	name: 'textarea',
	label: 'Here Is A Textarea',
	control: 'textarea',
	type: 'textarea',
	order: 0,
	location: null,
	width: '75',
	help: 'This is more help text',
	default: 'This is an example default value',
	placeholder: 'Here is some placeholder text',
	maxlength: 3,
	number_rows: 4,
	new_lines: 'autop',
	parent: 'repeater',
};

const color = {
	name: 'color',
	label: 'This is a label for color',
	control: 'color',
	type: 'string',
	order: 1,
	location: null,
	width: '100',
	help: 'Here is some help text',
	default: '#ffffff',
	parent: 'repeater',
};

const blockLabBlocks = {
	'block-lab/test-repeater': {
		name: blockName,
		title: blockTitle,
		excluded: [],
		icon: 'block_lab',
		category: {
			slug: 'layout',
			title: 'Layout Elements',
			icon: null,
		},
		keywords: [ 'repeater', 'panel' ],
		fields: {
			repeater: {
				name: 'repeater',
				label: 'Repeater',
				control: 'repeater',
				type: 'object',
				order: 0,
				help: 'Some example help text',
				min: minSubFields,
				max: maxSubFields,
				sub_fields: {
					textarea,
					color,
				},
			},
		},
	},
};

const blockLab = {
	authorBlocks: [ blockSlug ],
	postType: 'post',
};

describe( 'RepeaterBlock', () => {
	it( 'displays the repeater block in the inserter and the block has the expected values', () => {
		const { getAllByPlaceholderText, getAllByLabelText, getByLabelText } = render(
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
		const textAreaField = blockEdit.querySelector( 'textarea' );

		// The block should have the values from blockLabBlocks.
		expect( hasText( blockEdit, help ) ).toStrictEqual( true );
		expect( hasText( blockEdit, textarea.help ) ).toStrictEqual( true );
		expect( hasText( blockEdit, color.help ) ).toStrictEqual( true );
		expect( textAreaField.value ).toStrictEqual( textarea.default );

		const enteredText = 'This is some entered text';
		fireEvent.input( textAreaField, { target: { value: enteredText } } );

		// Click inside and outside the block.
		fireEvent.click( textAreaField );
		fireEvent.click( document.querySelector( '.editor-default-block-appender__content' ) );

		// The <ServerSideRender> should now display, instead of the block's field.
		waitFor( () => expect( document.querySelector( '.block-lab-editor__ssr' ) ).toBeInTheDocument() );

		// Click the block again.
		fireEvent.click( blockEdit );

		// The text entered in the <textarea> should still be there.
		expect( textAreaField.value ).toStrictEqual( enteredText );

		// Click the repeater button to add a new subfield.
		fireEvent.click( getByLabelText( 'Add new' ) );

		// There should now be 2 rows of subfields.
		expect( getAllByPlaceholderText( textarea.placeholder ) ).toHaveLength( 2 );

		fireEvent.click( getByLabelText( 'Add new' ) );
		fireEvent.click( getByLabelText( 'Add new' ) );

		// There should not be more than the max number of subfields, no matter how many times 'Add new' is clicked.
		expect( getAllByPlaceholderText( textarea.placeholder ) ).toHaveLength( maxSubFields );

		// Delete a subfield.
		fireEvent.mouseOver( textAreaField );
		fireEvent.click( getAllByLabelText( 'Delete' )[ 0 ] );

		// There should now be one less subfield.
		expect( getAllByPlaceholderText( textarea.placeholder ) ).toHaveLength( 2 );

		fireEvent.click( getAllByLabelText( 'Delete' )[ 0 ] );
		fireEvent.click( getAllByLabelText( 'Delete' )[ 0 ] );

		// There should not be less than the minimum number of subfields, no matter how many times 'Delete' is pressed.
		expect( getAllByPlaceholderText( textarea.placeholder ) ).toHaveLength( minSubFields );
	} );
} );

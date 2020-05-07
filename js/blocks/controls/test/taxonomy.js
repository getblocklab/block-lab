/**
 * External dependencies
 */
import '@testing-library/jest-dom/extend-expect';
import { fireEvent, getByText, render, waitFor } from '@testing-library/react';

/**
 * WordPress dependencies
 */
import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import BlockLabTaxonomyControl from '../taxonomy';

jest.mock( '@wordpress/api-fetch' );

const props = {
	field: {
		label: 'This is a label for the taxonomy field',
		help: 'Also, here is some help text',
		post_type_rest_slug: 'tags',
	},
	getValue: jest.fn(),
	onChange: jest.fn(),
};

describe( 'Taxonomy', () => {
	it( 'has the label', () => {
		const { getByLabelText } = render( <BlockLabTaxonomyControl { ...props } /> );
		expect( getByLabelText( props.field.label ) ).toBeInTheDocument();
	} );

	it( 'has the help text', () => {
		render( <BlockLabTaxonomyControl { ...props } /> );
		expect( getByText( document, props.field.help ) ).toBeInTheDocument();
	} );

	it( 'sends a new value to the onChange handler', async () => {
		render( <BlockLabTaxonomyControl { ...props } /> );
		const input = document.querySelector( 'input' );
		const taxonomy = {
			id: 921,
			name: 'exampletag',
		};

		// Mock the API fetch function that gets the taxonomies.
		apiFetch.mockImplementationOnce(
			() => new Promise( ( resolve ) => resolve( [ taxonomy ] ) )
		);

		// Focus the <input>, so the popover appears with taxonomy suggestion(s).
		fireEvent.focus( input );

		// Click to select a taxonomy.
		await waitFor( () =>
			fireEvent.click( getByText( document, taxonomy.name ) )
		);

		// The onChange handler should be called with the selected taxonomy.
		expect( props.onChange ).toHaveBeenCalledWith( { id: taxonomy.id, name: taxonomy.name } );
	} );
} );

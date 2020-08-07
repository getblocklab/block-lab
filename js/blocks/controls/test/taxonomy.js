/**
 * External dependencies
 */
import { fireEvent, render, waitFor } from '@testing-library/react';

/**
 * WordPress dependencies
 */
import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import BlockLabTaxonomyControl from '../taxonomy';

jest.mock( '@wordpress/api-fetch' );

test( 'taxonomy control', async () => {
	const props = {
		field: {
			label: 'This is a label for the taxonomy field',
			help: 'Also, here is some help text',
			post_type_rest_slug: 'tags',
		},
		getValue: jest.fn(),
		onChange: jest.fn(),
	};
	const { getByLabelText, getByRole, getByText } = render(
		<BlockLabTaxonomyControl { ...props } />
	);

	getByLabelText( props.field.label );
	getByText( props.field.help );

	const taxonomy = {
		id: 921,
		name: 'exampletag',
	};

	// Mock the API fetch function that gets the taxonomies.
	apiFetch.mockImplementationOnce(
		() => new Promise( ( resolve ) => resolve( [ taxonomy ] ) )
	);

	// Focus the <input>, so the popover appears with taxonomy suggestion(s).
	const input = getByRole( 'combobox' );
	fireEvent.focus( input );

	// Click to select a taxonomy.
	await waitFor( () => fireEvent.click( getByText( taxonomy.name ) ) );

	// The onChange handler should be called with the selected taxonomy.
	expect( props.onChange ).toHaveBeenCalledWith( {
		id: taxonomy.id,
		name: taxonomy.name,
	} );
} );

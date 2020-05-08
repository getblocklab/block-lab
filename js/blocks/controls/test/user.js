/**
 * External dependencies
 */
import { fireEvent, getByText, render, waitFor } from '@testing-library/react';

/**
 * WordPress dependencies
 */
import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import BlockLabUserControl from '../user';

jest.mock( '@wordpress/api-fetch' );

const props = {
	field: {
		label: 'This is a user field',
		help: 'This is help text for the user field',
	},
	getValue: jest.fn(),
	onChange: jest.fn(),
};

describe( 'User', () => {
	it( 'has the label', () => {
		const { getByLabelText } = render( <BlockLabUserControl { ...props } /> );
		expect( getByLabelText( props.field.label ) ).toBeInTheDocument();
	} );

	it( 'has the help text', () => {
		render( <BlockLabUserControl { ...props } /> );
		expect( getByText( document, props.field.help ) ).toBeInTheDocument();
	} );

	it( 'sends a new value to the onChange handler', async () => {
		render( <BlockLabUserControl { ...props } /> );
		const input = document.querySelector( 'input' );
		const user = {
			id: 32,
			name: 'Jose Adams',
		};

		// Mock the API fetch function that gets the users.
		apiFetch.mockImplementationOnce(
			() => new Promise( ( resolve ) => resolve( [ user ] ) )
		);

		// Focus the <input>, so the popover appears with user suggestion(s).
		fireEvent.focus( input );

		// Click to select a user.
		await waitFor( () =>
			fireEvent.click( getByText( document, user.name ) )
		);

		// The onChange handler should be called with the selected user.
		expect( props.onChange ).toHaveBeenCalledWith( { id: user.id, userName: user.name } );
	} );
} );

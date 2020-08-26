/**
 * External dependencies
 */
import { fireEvent, render, waitFor } from '@testing-library/react';
import user from '@testing-library/user-event';

/**
 * WordPress dependencies
 */
import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import { GetGenesisPro } from '../';

jest.mock( '@wordpress/api-fetch' );

const couponCode = '13543234';
window.blockLabMigration = { couponCode };

test( 'get Genesis Pro migration step', async () => {
	apiFetch.mockImplementation( () => new Promise( ( resolve ) => resolve( { success: true } ) ) );

	const props = {
		goToNext: jest.fn(),
		goToPrevious: jest.fn(),
		isStepActive: true,
		isStepComplete: false,
		stepIndex: 1,
	};
	const { getByText, getByRole } = render( <GetGenesisPro { ...props } /> );

	getByText( couponCode );

	// Because the checkbox isn't checked, the 'next' button should be disabled.
	user.click( getByText( 'Next Step' ) );
	expect( props.goToNext ).not.toHaveBeenCalled();

	user.click( getByText( 'Save' ) );
	getByText( 'The subscription key is empty.' );

	fireEvent.change(
		getByRole( 'textbox' ),
		{ target: { value: '1234567' } }
	);

	await waitFor( () =>
		user.click( getByText( 'Save' ) )
	);
	getByText( 'Thanks, the key is valid.' );

	user.click( getByText( 'Next Step' ) );
	expect( props.goToNext ).toHaveBeenCalled();
} );

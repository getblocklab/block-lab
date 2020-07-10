/**
 * External dependencies
 */
import { render } from '@testing-library/react';
import user from '@testing-library/user-event';

/**
 * Internal dependencies
 */
import { GetGenesisPro } from '../';

const couponCode = '13543234';
window.blockLabMigration = { couponCode };

test( 'get Genesis Pro migration step', async () => {
	const props = {
		currentStepIndex: 1,
		stepIndex: 1,
		goToNext: jest.fn(),
		goToPrevious: jest.fn(),
	};
	const { getByLabelText, getByText } = render( <GetGenesisPro { ...props } /> );

	getByText( couponCode );

	// Because the checkbox isn't checked, the 'next' button should be disabled.
	user.click( getByText( 'Next Step' ) );
	expect( props.goToNext ).not.toHaveBeenCalled();

	// Now that the 'confirm' checkbox is checked, the 'next' button should work.
	user.click( getByLabelText( 'Migrate without Genesis Pro.' ) );
	user.click( getByText( 'Next Step' ) );
	expect( props.goToNext ).toHaveBeenCalled();
} );

/**
 * External dependencies
 */
import { render } from '@testing-library/react';
import user from '@testing-library/user-event';

/**
 * Internal dependencies
 */
import { UpdateHooks } from '../';

test( 'update hooks migration step', async () => {
	const props = {
		currentStepIndex: 2,
		stepIndex: 2,
		goToNext: jest.fn(),
		goToPrevious: jest.fn(),
	};
	const { getByText } = render( <UpdateHooks { ...props } /> );

	getByText( 'Update Hooks & API' );
	getByText( props.stepIndex.toString() );

	// It should always be possible to click the 'previous' button.
	user.click( getByText( 'Previous' ) );
	expect( props.goToPrevious ).toHaveBeenCalled();
} );

/**
 * External dependencies
 */
import { render, screen } from '@testing-library/react';
import user from '@testing-library/user-event';

/**
 * Internal dependencies
 */
import { UpdateHooks } from '../';

describe( 'update hooks migration step', () => {
	it( 'displays step content when this step is active', async () => {
		const props = {
			goToNext: jest.fn(),
			goToPrevious: jest.fn(),
			isStepActive: true,
			isStepComplete: false,
			stepIndex: 2,
		};
		const { getByText } = render( <UpdateHooks { ...props } /> );

		getByText( 'Update Hooks & API' );
		getByText( props.stepIndex.toString() );

		// It should always be possible to click the 'previous' button.
		user.click( getByText( 'Previous' ) );
		expect( props.goToPrevious ).toHaveBeenCalled();
	} );

	it( 'does not display content when this step is not active', async () => {
		const props = {
			goToNext: jest.fn(),
			goToPrevious: jest.fn(),
			isStepActive: false,
			isStepComplete: false,
			stepIndex: 2,
		};
		const { getByText } = render( <UpdateHooks { ...props } /> );

		// The heading should still display.
		getByText( 'Update Hooks & API' );
		getByText( props.stepIndex.toString() );

		// The content of the step should now display, as it's not active.
		expect( screen.queryByText( 'Previous' ) ).not.toBeInTheDocument();
	} );
} );

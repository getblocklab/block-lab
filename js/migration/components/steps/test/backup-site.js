/**
 * External dependencies
 */
import { render } from '@testing-library/react';
import user from '@testing-library/user-event';

/**
 * Internal dependencies
 */
import BackupSite from '../backup-site';

test( 'backup site step', async () => {
	const props = {
		currentStepIndex: 1,
		stepIndex: 1,
		goToNext: jest.fn(),
	};
	const { getByLabelText, getByText } = render( <BackupSite { ...props } /> );

	getByText( /backup your site/ );
	getByText( props.stepIndex.toString() );

	// Because the 'confirm' checkbox isn't checked, the 'next' button should be disabled.
	user.click( getByText( 'Next Step' ) );
	expect( props.goToNext ).not.toHaveBeenCalled();

	// Now that the 'confirm' checkbox is checked, the 'next' button should work.
	user.click( getByLabelText( 'I have backed up my site.' ) );
	user.click( getByText( 'Next Step' ) );
	expect( props.goToNext ).toHaveBeenCalled();
} );

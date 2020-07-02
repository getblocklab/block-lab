/**
 * External dependencies
 */
import { render } from '@testing-library/react';
import user from '@testing-library/user-event';

/**
 * Internal dependencies
 */
import { MigrateBlocks } from '../';

test( 'migrate blocks step', async () => {
	const props = {
		currentStepIndex: 4,
		stepIndex: 4,
	};
	const { getByText } = render( <MigrateBlocks { ...props } /> );

	getByText( 'Migrate your Blocks' );
	getByText( props.stepIndex.toString() );

	user.click( getByText( 'Migrate Now' ) );
	expect( getByText( 'Migrating your blocks...' ) ).toBeInTheDocument();
} );

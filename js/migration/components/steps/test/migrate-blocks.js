/**
 * External dependencies
 */
import { render, waitFor } from '@testing-library/react';
import user from '@testing-library/user-event';

/**
 * WordPress dependencies
 */
import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import { MigrateBlocks } from '../';

jest.mock( '@wordpress/api-fetch' );

test( 'migrate blocks step', async () => {
	apiFetch.mockImplementation( () => new Promise( ( resolve ) => resolve( { success: true } ) ) );
	const props = {
		currentStepIndex: 4,
		stepIndex: 4,
		goToNext: jest.fn(),
	};

	const { getByText } = render( <MigrateBlocks { ...props } /> );

	getByText( 'Migrate your Blocks' );
	getByText( props.stepIndex.toString() );

	await waitFor( () =>
		user.click( getByText( 'Migrate Now' ) )
	);

	expect( getByText( 'Migrating your post content...' ) ).toBeInTheDocument();
} );

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
global.blockLabMigration = {
	gcbUrl: 'https://example.com',
};

test( 'migrate blocks step', async () => {
	apiFetch.mockImplementation( () => new Promise( ( resolve ) => resolve( { success: true } ) ) );
	const props = {
		currentStepIndex: 4,
		goToNext: jest.fn(),
		isStepActive: true,
		isStepComplete: false,
		stepIndex: 4,
	};

	const { getByText } = render( <MigrateBlocks { ...props } /> );

	getByText( /migrate your blocks/i );
	getByText( props.stepIndex.toString() );

	await waitFor( () =>
		user.click( getByText( 'Migrate Now' ) )
	);

	expect( getByText( 'The migration was successful!' ) ).toBeInTheDocument();
} );

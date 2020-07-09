/**
 * External dependencies
 */
import { render } from '@testing-library/react';
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

apiFetch.mockImplementationOnce(
	() => new Promise( ( resolve ) => resolve( {} ) )
);

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

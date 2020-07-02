/**
 * External dependencies
 */
import { render } from '@testing-library/react';

/**
 * Internal dependencies
 */
import App from '../app';

global.blockLabMigration = {
	isPro: true,
};

test( 'migration app', async () => {
	const { getByText } = render( <App /> );

	expect( getByText( 'Migrating to Genesis Custom Blocks' ) ).toBeInTheDocument();
	expect( getByText( 'Need to let the developer for this site know about this? Send them this link.' ) ).toBeInTheDocument();
} );

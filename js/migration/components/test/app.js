/**
 * External dependencies
 */
import { render } from '@testing-library/react';

/**
 * Internal dependencies
 */
import App from '../app';

test( 'migration app', async () => {
	const { getByText } = render( <App /> );
	expect( getByText( 'This is the beginning of the migration UI' ) ).toBeInTheDocument();
} );

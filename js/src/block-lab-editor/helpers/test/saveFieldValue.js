/**
 * Internal dependencies
 */
import saveFieldValue from '../saveFieldValue';

describe( 'saveFieldValue', () => {
	it( 'should have used the store', () => {
		saveFieldValue( 'real-field-slug', 'example-email', 'you@example.com' );
		expect( global.wp.data.dispatch ).toHaveBeenCalledWith( 'core/editor' );
	} );
} );

/**
 * Internal dependencies
 */
import saveFieldValue from '../saveFieldValue';

describe( 'saveFieldValue', () => {
	it( 'should not update the post if the field argument is an empty string', () => {
		saveFieldValue( '', 'example-email', 'you@example.com' );
		expect( global.wp.data.dispatch ).toHaveBeenCalledTimes( 0 );
	} );

	it( 'should not attempt to update the post if there is no fields index', () => {
		saveFieldValue( 'real-field-slug', 'example-email', 'you@example.com' );
		expect( global.wp.data.dispatch ).toHaveBeenCalledTimes( 0 );
	} );

	it( 'should not update the post if the field does not exist yet', () => {
		saveFieldValue( 'real-field-slug', 'example-email', 'you@example.com' );
		expect( global.wp.data.dispatch ).toHaveBeenCalledTimes( 0 );
	} );
} );

/**
 * Internal dependencies
 */
import removeSlugFormat from '../removeSlugFormat';

describe( 'removeSlugFormat', () => {
	it.each( [
		[ 'new-field', 'New Field' ],
		[ 'new-field-1', 'New Field 1' ],
		[ 'new-field-32', 'New Field 32' ],
		[ 'exampleblock', 'Exampleblock' ],
		[ 'example-block', 'Example Block' ],
	] )( 'should return the proper slug',
		( text, expected ) => {
			expect( removeSlugFormat( text ) ).toStrictEqual( expected );
		}
	);
} );

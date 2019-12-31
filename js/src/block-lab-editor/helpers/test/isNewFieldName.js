/**
 * Internal dependencies
 */
import isNewFieldName from '../isNewFieldName';

describe( 'isNewFieldName', () => {
	it.each( [
		[ 'new-field', true ],
		[ 'new-field-1', true ],
		[ 'new-field-52', true ],
		[ 'new-field-6252', true ],
		[ 'anew-field-1', false ],
		[ 'new-fiel-1', false ],
		[ 'newField-1', false ],
		[ 'new-Field-1', false ],
	] )( 'should get whether this is a new field name',
		( fieldName, expected ) => {
			expect( isNewFieldName( fieldName ) ).toStrictEqual( expected );
		}
	);
} );

/**
 * Internal dependencies
 */
import slugify from '../slugify';

describe( 'slugify', () => {
	it.each( [
		[ 'Example Block', 'example-block' ],
		[ 'Example  Block', 'example-block' ],
		[ 'ExampleBlock', 'exampleblock' ],
		[ 'Example_Block', 'example-block' ],
		[ 'Long Block Name In Title Case', 'long-block-name-in-title-case' ],
		[ 'With A Number 2352', 'with-a-number-2352' ],
		[ '* Example Block *', '-example-block-' ],
		[ 'Block %!%#@)', 'block-' ],
	] )( 'should return the proper slug',
		( text, expected ) => {
			expect( slugify( text ) ).toStrictEqual( expected );
		}
	);
} );

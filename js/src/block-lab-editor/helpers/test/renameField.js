/**
 * Internal dependencies
 */
import renameField from '../renameField';

describe( 'renameField', () => {
	it.each( [
		[
			{ fields: { foo: { label: 'example-label' } } },
			'foo',
			'',
			{ fields: { '': { label: 'example-label', name: '' } } }
		],
		[
			{ fields: { foo: { label: 'example-label' } } },
			'foo',
			'new-name',
			{ fields: { 'new-name': { label: 'example-label', name: 'new-name' } } }
		],
	] )( 'should properly rename the field',
		( block, previousSlug, newSlug, expected ) => {
			expect( renameField( block, previousSlug, newSlug ) ).toStrictEqual( expected );
		}
	);
} );

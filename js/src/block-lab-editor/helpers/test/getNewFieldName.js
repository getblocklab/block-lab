/**
 * Internal dependencies
 */
import { getNewFieldName } from '../';

describe( 'getNewFieldName', () => {
	it.each( [
		[
			{ fields: { baz: { label: 'baz' } } },
			'new-field',
		],
		[
			{ fields: { 'new-field': { label: 'baz' } } },
			'new-field-1',
		],
		[
			{ fields: { 'new-field': { label: 'baz' }, 'new-field-1': { label: 'foo' } } },
			'new-field-2',
		],
		[
			{ fields: { 'new-field-11': { label: 'baz' }, 'new-field-22': { label: 'foo' } } },
			'new-field-23',
		],
	] )( 'should get the correct new name',
		( block, expected ) => {
			expect( getNewFieldName( block ) ).toStrictEqual( expected );
		}
	);
} );

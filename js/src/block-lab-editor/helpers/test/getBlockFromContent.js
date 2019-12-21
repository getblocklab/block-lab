/**
 * Internal dependencies
 */
import getBlockFromContent from '../getBlockFromContent';

describe( 'getBlockFromContent', () => {
	const mockValue = { foo: 'example', bar: 'baz' };

	it( 'should return false if the content is an empty string', () => {
		expect( getBlockFromContent( '' ) ).toStrictEqual( false );
	} );

	it( 'should return false when passed invalid JSON, and not throw an error', () => {
		expect( getBlockFromContent( '{"0: "example value"' ) ).toStrictEqual( false );
	} );

	it( 'should return false if there is no value at the "0" index', () => {
		expect( getBlockFromContent( JSON.stringify( { 1: mockValue } ) ) ).toStrictEqual( mockValue );
	} );

	it( 'should return the value at the "0" index', () => {
		expect( getBlockFromContent( JSON.stringify( { 0: mockValue } ) ) ).toStrictEqual( mockValue );
	} );
} );

/**
 * Internal dependencies
 */
import { getSimplifiedFields } from '../';

describe( 'getBlockFromContent', () => {
	it( 'should return an empty array if passed an empty object', () => {
		expect( getSimplifiedFields( {} ) ).toStrictEqual( [] );
	} );

	it( 'should return simplified fields for an object of 3 fields', () => {
		expect( getSimplifiedFields( {
			example_post: {
				type: 'post',
				help: 'This is some example help text',
				location: 'editor',
				post_type: 'posts',
				width: '100',
			},
			example_classic_text: {
				type: 'classic_text',
				default: 'https://example.com/go-here',
				help: 'Here is the help text',
				location: 'editor',
			},
			example_user: {
				type: 'user',
				default: 'https://example.com/go-here',
				help: 'Here is the help text',
				location: 'inspector',
			},
		} ) ).toStrictEqual( [
			{
				name: 'example_post',
				type: 'post',
				help: 'This is some example help text',
				location: 'editor',
				post_type: 'posts',
				width: '100',
			},
			{
				name: 'example_classic_text',
				type: 'classic_text',
				default: 'https://example.com/go-here',
				help: 'Here is the help text',
				location: 'editor',
			},
			{
				name: 'example_user',
				type: 'user',
				default: 'https://example.com/go-here',
				help: 'Here is the help text',
				location: 'inspector',
			},
		] );
	} );

	it( 'should still include falsy values in the simplified fields', () => {
		expect( getSimplifiedFields( {
			test_taxonomy: {
				default: '',
			},
		} ) ).toStrictEqual( [
			{
				name: 'test_taxonomy',
				default: '',
			},
		] );
	} );
} );

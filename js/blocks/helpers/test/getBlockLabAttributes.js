/**
 * Internal dependencies
 */
import { getBlockLabAttributes } from '../';

describe( 'getBlockFromContent', () => {
	const fieldsWithOnlyType = {
		example_email: {
			type: 'email',
		},
		example_post: {
			type: 'post',
		},
	};

	it( 'should return an empty object if passed an empty object', () => {
		expect( getBlockLabAttributes( {} ) ).toStrictEqual( {} );
	} );

	it( 'should not throw an error if certain attributes are not present', () => {
		expect( getBlockLabAttributes( fieldsWithOnlyType ) ).toStrictEqual( fieldsWithOnlyType );
	} );

	it( 'should return only the attributes of the fields', () => {
		expect( getBlockLabAttributes( {
			example_text: {
				type: 'text',
				default: 'Here is some text',
				help: 'This is the help text',
				location: 'editor',
			},
			example_url: {
				type: 'url',
				default: 'https://example.com/go-here',
				help: 'Here is the help text',
				location: 'inspector',
			},
		} ) ).toStrictEqual( {
			example_text: {
				type: 'text',
				default: 'Here is some text',
			},
			example_url: {
				type: 'url',
				default: 'https://example.com/go-here',
			},
		} );
	} );
} );

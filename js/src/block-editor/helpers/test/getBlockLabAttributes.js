/**
 * Internal dependencies
 */
import getBlockLabAttributes from '../getBlockLabAttributes';

describe( 'getBlockFromContent', () => {
	const mockFields = {
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
	};

	const expectedAttributes = {
		example_text: {
			type: 'text',
			default: 'Here is some text',
		},
		example_url: {
			type: 'url',
			default: 'https://example.com/go-here',
		},
	};

	it( 'should return an empty object if passed an empty object', () => {
		expect( getBlockLabAttributes( {} ) ).toStrictEqual( {} );
	} );

	it( 'should return only the attributes of the fields', () => {
		expect( getBlockLabAttributes( mockFields ) ).toStrictEqual( expectedAttributes );
	} );
} );

/**
 * Internal dependencies
 */
import registerBlocks from '../registerBlocks';
const mockRegisterBlockType = jest.fn();

describe( 'registerBlocks', () => {
	const Edit = () => {};

	it( 'should not register any block if there are no Block Lab blocks passed', () => {
		registerBlocks( {}, {}, Edit );
		expect( mockRegisterBlockType ).toHaveBeenCalledTimes( 0 );
	} );
} );

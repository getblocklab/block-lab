/**
 * Internal dependencies
 */
import saveBlock from '../saveBlock';

describe( 'saveBlock', () => {
	it( 'should save an empty block correctly', () => {
		saveBlock( {} );
		expect( global.wp.data.dispatch ).toHaveBeenCalledWith( 'core/editor' );
		expect( global.wp.data.dispatch ).toHaveBeenCalledWith( 'core/block-editor' );
	} );
} );

/**
 * Internal dependencies
 */
import { addNewField } from '../';

describe( 'addNewField', () => {
	it( 'should not update the post if the post does not have a block', () => {
		addNewField();
		expect( global.wp.data.dispatch( 'core/editor' ).editPost ).toHaveBeenCalledTimes( 0 );
	} );
} );

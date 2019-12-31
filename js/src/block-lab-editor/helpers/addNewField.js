/**
 * WordPress dependencies
 */
const { select } = wp.data;

/**
 * Internal dependencies
 */
import getBlockFromContent from './getBlockFromContent';
import saveBlock from './saveBlock';
import { NEW_FIELD_NAME } from '../constants';

/**
 * Parses the block from the post content into an object.
 *
 * @return {boolean} Whether saving the field value succeeded.
 */
const addNewField = () => {
	const content = select( 'core/editor' ).getEditedPostContent();
	const block = getBlockFromContent( content ) || {};
	if ( ! block.hasOwnProperty( 'fields' ) ) {
		block.fields = {};
	}

	block.fields[ NEW_FIELD_NAME ] = { name: NEW_FIELD_NAME };
	saveBlock( block );

	return true;
};

export default addNewField;

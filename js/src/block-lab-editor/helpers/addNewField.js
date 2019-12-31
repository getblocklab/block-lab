/**
 * WordPress dependencies
 */
const { select } = wp.data;

/**
 * Internal dependencies
 */
import { getBlockFromContent, getNewFieldName, removeSlugFormat, saveBlock } from './';

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

	const newFieldName = getNewFieldName( block );
	block.fields[ newFieldName ] = {
		name: newFieldName,
		label: removeSlugFormat( newFieldName ),
	};
	saveBlock( block );

	return true;
};

export default addNewField;

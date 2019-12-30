/**
 * WordPress dependencies
 */
const { dispatch, select } = wp.data;

/**
 * Internal dependencies
 */
import getBlockFromContent from './getBlockFromContent';

/**
 * Parses the block from the post content into an object.
 *
 * @param {string} fieldSlug The slug of the field.
 * @param {string} key The key of the field value to change, like 'label'.
 * @param {string} value The new field value.
 * @return {boolean} Whether saving the field value succeeded.
 */
const saveFieldValue = ( fieldSlug, key, value ) => {
	if ( ! fieldSlug ) {
		return false;
	}

	const content = select( 'core/editor' ).getEditedPostContent();
	const block = getBlockFromContent( content ) || {};
	if ( ! block.hasOwnProperty( 'fields' ) ) {
		block.fields = {};
	}

	if ( ! block.fields.hasOwnProperty( fieldSlug ) || 'object' !== typeof block.fields[ fieldSlug ] ) {
		return false;
	}

	block.fields[ fieldSlug ][ key ] = value;
	dispatch( 'core/editor' ).editPost( { content: JSON.stringify( [ block ] ) } );
	dispatch( 'core/block-editor' ).resetBlocks( [] ); // Prevent the block editor from overwriting the saved content.
	return true;
};

export default saveFieldValue;

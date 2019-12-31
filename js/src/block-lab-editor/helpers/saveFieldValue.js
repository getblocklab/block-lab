/**
 * WordPress dependencies
 */
const { select } = wp.data;

/**
 * Internal dependencies
 */
import { getBlockFromContent, renameField, saveBlock, slugify } from './';

/**
 * Parses the block from the post content into an object.
 *
 * @param {string} fieldSlug The slug of the field.
 * @param {string} key The key of the field value to change, like 'label'.
 * @param {string} value The new field value.
 * @param {boolean} doSlugify Whether to slugify if this is a 'label' field.
 */
const saveFieldValue = ( fieldSlug, key, value, doSlugify = false ) => {
	const content = select( 'core/editor' ).getEditedPostContent();
	let block = getBlockFromContent( content ) || {};

	// @todo: remove this and mock getBlockFromContent().
	if ( ! block.hasOwnProperty( 'fields' ) ) {
		block.fields = {};
	}

	if ( ! block.fields.hasOwnProperty( fieldSlug ) ) {
		block.fields[ fieldSlug ] = {};
	}

	block.fields[ fieldSlug ][ key ] = value;

	// Editing the label for the first name changes the field name to be the same as the label.
	if ( 'label' === key && doSlugify ) {
		block = renameField( block, fieldSlug, slugify( value ) );
	}

	if ( 'name' === key ) {
		block = renameField( block, fieldSlug, value );
	}

	saveBlock( block );
};

export default saveFieldValue;

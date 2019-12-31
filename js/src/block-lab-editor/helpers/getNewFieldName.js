/**
 * Internal dependencies
 */
import { NEW_FIELD_NAME_BASE } from '../constants';

/**
 * Gets the name of a new field.
 *
 * This accounts for other fields that also start with 'new-field'.
 * For example, if there is one field named 'new-field-1',
 * this will return 'new-field-2'.
 *
 * @param {Object} block The block, including fields.
 * @return {string} The name of the new field.
 */
const getNewFieldName = ( block ) => {
	let highestIndex = 0;
	for ( const fieldName in block.fields ) {
		const matches = fieldName.match( new RegExp( `^${ NEW_FIELD_NAME_BASE }-([0-9]+)$` ) );
		if ( matches && matches[ 1 ] && Number( matches[ 1 ] ) > highestIndex ) {
			highestIndex = Number( matches[ 1 ] );
		}
	}

	if ( 0 === highestIndex ) {
		if ( block.fields[ NEW_FIELD_NAME_BASE ] ) {
			return `${ NEW_FIELD_NAME_BASE }-1`;
		}
		return NEW_FIELD_NAME_BASE;
	}

	const newIndex = highestIndex + 1;
	return `${ NEW_FIELD_NAME_BASE }-${ newIndex }`;
};

export default getNewFieldName;

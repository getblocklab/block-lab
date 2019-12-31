/**
 * Internal dependencies
 */
import { NEW_FIELD_NAME_BASE } from '../constants';

/**
 * Gets whether the field name is from a new field.
 *
 * @param {Object} fieldName The name of the field.
 * @return {boolean} Whether the name is from a new field.
 */
const isNewFieldName = ( fieldName ) => {
	return !! fieldName.match( new RegExp( `^${ NEW_FIELD_NAME_BASE }` ) );
};

export default isNewFieldName;

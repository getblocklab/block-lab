/**
 * Internal dependencies
 */
import { simplifiedFields } from '../loader/fields';
import { Control } from './';

/**
 * Renders the fields, using their control functions.
 *
 * @param {Array}  fields           The fields to render.
 * @param {Object} parentBlockProps The props to pass to the control function.
 * @param {Object} parentBlock      The block where the fields are.
 * @param {String} rowName          The name of the repeater row, if this field is in one (optional).
 * @return {Function} fields The rendered fields.
 */
export default ( { fields, parentBlockProps, parentBlock, rowName = null } ) => {
	return simplifiedFields( fields, rowName ).map( ( field, index ) => {
		return (
			<Control
				parentBlock={ parentBlock }
				parentBlockProps={ parentBlockProps }
				field={ field }
				index={ index }
				key={ field.name }
			/>
		);
	} );
};

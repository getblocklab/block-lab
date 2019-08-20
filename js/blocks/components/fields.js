/**
 * Internal dependencies
 */
import simplifiedFields from '../loader/fields';
import { ControlContainer } from './';

/**
 * Renders the fields, using their control functions.
 *
 * @param {Array}  fields           The fields to render.
 * @param {Object} parentBlockProps The props to pass to the control function.
 * @param {Object} parentBlock      The block where the fields are.
 * @param {string} rowIndex         The name of the repeater row, if this field is in one (optional).
 * @return {Function} fields The rendered fields.
 */
const Fields = ( { fields, parentBlockProps, parentBlock, rowIndex } ) => {
	return simplifiedFields( fields, rowIndex ).map( ( field ) => {

		/**
		 * Handles a control value changing.
		 *
		 * @param {mixed} newValue The new control value.
		 */
		const onChange = ( newValue ) => {
			const attr = { ...parentBlockProps.attributes };
			const { setAttributes } = parentBlockProps;

			if ( undefined !== rowIndex ) { // This is in a repeater row.
				const rows = attr[ field.parent ];

				/*
				* Copy the rows array, so the change is recognized.
				* @see https://github.com/WordPress/gutenberg/issues/7016#issuecomment-396094836
				*/
				const rowsCopy = rows.slice();
				if ( ! rowsCopy[ rowIndex ] ) {
					rowsCopy[ rowIndex ] = {};
				}
				rowsCopy[ rowIndex ][ field.name ] = newValue;
				attr[ field.parent ] = rowsCopy;
			} else { // This is not in a repeater row.
				attr[ field.name ] = newValue;
			}

			setAttributes( attr );
		};

		return (
			<ControlContainer
				parentBlock={ parentBlock }
				parentBlockProps={ parentBlockProps }
				field={ field }
				rowIndex={ rowIndex }
				key={ field.name }
				onChange={ onChange }
			/>
		);
	} );
};

export default Fields;

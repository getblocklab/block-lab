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
 * @param {number} rowIndex         The index of the repeater row, if this field is in one (optional).
 * @return {Function} fields The rendered fields.
 */
const Fields = ( { fields, parentBlockProps, parentBlock, rowIndex } ) => {
	return simplifiedFields( fields ).map( ( field ) => {

		/**
		 * Handles a single control value changing.
		 *
		 * Changing a control value inside a block needs to be able to call
		 * the block's setAttributes property, so that the block can save the value.
		 * This function is passed to the control so that the control can save the value,
		 * depending on whether the control is in a repeater row or not.
		 *
		 * @param {mixed} newValue The new control value.
		 */
		const onChange = ( newValue ) => {
			const attr = { ...parentBlockProps.attributes };
			const { setAttributes } = parentBlockProps;

			if ( undefined !== rowIndex ) { // If this is in a repeater row.
				const rows = attr[ field.parent ] || [ {} ];

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
				parentBlockProps.setAttributes( attr );
			} else { // If this is not in a repeater row.
				attr[ field.name ] = newValue;
				setAttributes( attr );
			}
		};

		/**
		 * Gets the value of the Control function, given its properties.
		 *
		 * If this is in a repeater row, the value is appropriate for that.
		 *
		 * @param {Object} props The properties of the Control function.
		 */
		const getValue = ( props ) => {
			const { field, parentBlockProps, rowIndex } = props;
			const attr = { ...parentBlockProps.attributes };

			return field.parent && attr[ field.parent ] ? attr[ field.parent ][ rowIndex ][ field.name ] : attr[ field.name ];
		}

		return (
			<ControlContainer
				key={ field.name }
				parentBlock={ parentBlock }
				parentBlockProps={ parentBlockProps }
				field={ field }
				rowIndex={ rowIndex }
				getValue={ getValue }
				onChange={ onChange }
			/>
		);
	} );
};

export default Fields;

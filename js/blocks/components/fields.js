/**
 * WordPress dependencies
 */
import { applyFilters } from '@wordpress/hooks';
import { select } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { getSimplifiedFields } from '../helpers';
import controls from '../controls';

/**
 * Gets the control function for the field.
 *
 * @param {Object} field The field to get the control function of.
 * @return {Function} The control function.
 */
const getControl = ( field ) => {
	const loadedControls = applyFilters( 'block_lab_controls', controls );
	return loadedControls[ field.control ];
};

/**
 * Gets the class name for the field.
 *
 * @param {Object} field The field to get the class name of.
 * @return {string} The class name.
 */
const getClassName = ( field ) => {
	let className = 'block-lab-control';

	if ( field.width ) {
		className += ' width-' + field.width;
	}

	return className;
};

/**
 * Renders the fields, using their control functions.
 *
 * @param {Object} props                  The component props.
 * @param {Array}  props.fields           The fields to render.
 * @param {Object} props.parentBlockProps The props to pass to the control function.
 * @param {Object} props.parentBlock      The block where the fields are.
 * @param {number} props.rowIndex         The index of the repeater row, if this field is in one (optional).
 * @return {Function} fields The rendered fields.
 */
const Fields = ( { fields, parentBlockProps, parentBlock, rowIndex } ) => {
	return getSimplifiedFields( fields ).map( ( field ) => {
		if ( field.location && ! field.location.includes( 'editor' ) ) {
			return null; // This is not meant for the editor.
		}

		/**
		 * Handles a single control value changing.
		 *
		 * Changing a control value inside a block needs to be able to call
		 * the block's setAttributes property, so that the block can save the value.
		 * This function is passed to the control so that the control can save the value,
		 * depending on whether the control is in a repeater row or not.
		 * This gets the latest parentAttributes by using select( 'core/block-editor' ),
		 * as the parentBlockProps.attributes in the outer scope may be outdated.
		 *
		 * @param {*} newValue The new control value.
		 */
		const onChange = ( newValue ) => {
			const { clientId, setAttributes } = parentBlockProps;
			const parentAttributes = select( 'core/block-editor' ).getBlockAttributes( clientId );
			const attr = { ...parentAttributes };

			if ( undefined === rowIndex ) {
				// This is not in a repeater row.
				attr[ field.name ] = newValue;
				setAttributes( attr );
			} else {
				// This is in a repeater row.
				const attribute = attr[ field.parent ];
				const defaultRows = [ {} ];
				const rows = ( attribute && attribute.rows ) ? attribute.rows : defaultRows;

				if ( ! rows[ rowIndex ] ) {
					rows[ rowIndex ] = {};
				}
				rows[ rowIndex ][ field.name ] = newValue;
				attr[ field.parent ] = { rows };
				parentBlockProps.setAttributes( attr );
			}
		};

		/**
		 * Gets the value of the Control function, given its properties.
		 *
		 * If this is in a repeater row, the value is appropriate for that.
		 *
		 * @param {Object} props The properties of the Control function.
		 * @param {Object} props.field The field.
		 * @param {Object} props.parentBlockProps The props of the parent block.
		 * @param {number} props.rowIndex The index of the repeater row (optional).
		 */
		const getValue = ( {
			field: ownField,
			parentBlockProps: ownParentBlockProps,
			rowIndex: ownRowIndex,
		} ) => {
			const attr = { ...ownParentBlockProps.attributes };

			if ( ownField.parent && attr[ ownField.parent ] && attr[ ownField.parent ].rows ) {
				// The ownField is probably in a repeater row, as it has a parent.
				return attr[ ownField.parent ].rows[ ownRowIndex ][ ownField.name ];
			}
			// The ownField is not in a repeater row.
			return attr[ ownField.name ];
		};

		const Control = getControl( field );

		return !! Control && (
			<div className={ getClassName( field ) } key={ `${ field.name }-control-${ rowIndex }` }>
				<Control
					field={ field }
					getValue={ getValue }
					onChange={ onChange }
					parentBlock={ parentBlock }
					rowIndex={ rowIndex }
					parentBlockProps={ parentBlockProps }
				/>
			</div>
		);
	} );
};

export default Fields;

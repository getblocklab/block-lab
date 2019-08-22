/**
 * WordPress dependencies
 */
const { applyFilters } = wp.hooks;

/**
 * Internal dependencies
 */
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
  * Gets the control, based on the field values.
  *
  * @param {Object}        parentBlock      The block that has the control.
  * @param {Object}        parentBlockProps The block props.
  * @param {Object}        field            The field to render.
  * @param {number}        rowIndex         The index of the row, if this is in a row.
  * @param {Function}      onChange         The handler for when the value of the control changes.
  * @param {Function}      getValue         Gets the value of the control.
  * @return {Function|null} The control container.
  */
const ControlContainer = ( { parentBlock, parentBlockProps, field, rowIndex, onChange, getValue } ) => {
	if ( field.location && ! field.location.includes( 'editor' ) ) {
		return null; // This is not meant for the editor.
	}

	const Control = getControl( field );
	if ( ! Control ) {
		return null;
	}

	return (
		<Control
			key={ `${ field.name }-control-${ rowIndex }` }
			field={ field }
			getValue={ getValue }
			onChange={ onChange }
			parentBlock={ parentBlock }
			rowIndex={ rowIndex }
			parentBlockProps={ parentBlockProps }
		/>
	);
};

export default ControlContainer;

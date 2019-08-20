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
	if ( field.hasOwnProperty( 'controlFunction' ) ) {
		return field.controlFunction;
	}

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
  * @return {Function|null} The rendered control as JSX, or null.
  */
const ControlContainer = ( { parentBlock, parentBlockProps, field, rowIndex, onChange } ) => {
	if ( field.location && ! field.location.includes( 'editor' ) ) {
		return null; // This is not meant for the editor.
	}

	const Control = getControl( field );
	if ( ! Control ) {
		return null;
	}

	/**
	 * Gets the value of the Control function, given its properties.
	 *
	 * If this is in a repeater row, the value is appropriate for that.
	 *
	 * @param {Object} props The properties of the Control function.
	 */
	const getValue = ( props ) => {
		const { attributes, field, rowIndex } = props;
		const attr = { ...attributes };
		return field.parent && attr[ field.parent ] ? attr[ field.parent ][ rowIndex ][ field.name ] : attr[ field.name ];
	}
	const controlProps = { ...parentBlockProps, field, getValue, onChange, parentBlock, rowIndex };

	return (
		<div key={ `${ field.name }-control-${ rowIndex }` }>
			{ <Control { ...controlProps } /> }
		</div>
	)
};

export default ControlContainer;

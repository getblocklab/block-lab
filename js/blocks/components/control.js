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
const getControlFunction = ( field ) => {
	if ( field.hasOwnProperty( 'controlFunction' ) ) {
		return field.controlFunction;
	}

	const loadedControls = applyFilters( 'block_lab_controls', controls );
	return loadedControls[ field.control ];
};

 /**
 * Gets the rendered control, based on the field values.
 *
 * @param {Object}        parentBlock      The block that has the control.
 * @param {Object}        parentBlockProps The block props.
 * @param {Object}        field            The field to render.
 * @param {number|string} index            The index in the block, or the row name if one exists.
 * @return {Function|null} The rendered control as JSX, or null.
 */
export default ( { parentBlock, parentBlockProps, field } ) => {
	if ( field.location && ! field.location.includes( 'editor' ) ) {
		return null; // This is not meant for the editor.
	}

	const controlFunction = getControlFunction( field );
	const control = controlFunction ? controlFunction( parentBlockProps, field, parentBlock ) : null;

	return (
		<div key={ `${ field.name }-control` }>
			{ control }
		</div>
	)
};

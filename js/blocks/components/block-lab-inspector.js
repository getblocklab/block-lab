import simplifiedFields from '../loader/fields';
import controls from '../controls';

const { PanelBody } = wp.components;
const { InspectorControls } = wp.editor;
const { applyFilters } = wp.hooks;

/**
 * Gets the rendered control for the Inspector Controls, based on the field values.
 *
 * @param {Object} blockProps The block's props.
 * @param {Object} block The block.
 * @return {Function|null} The rendered control as JSX, or null.
 */
 export default ( { blockProps, block } ) => {
	const fields = simplifiedFields( block.fields ).map( field => {
		// If its not meant for the inspector then continue (return null).
		if ( ! field.location || ! field.location.includes( 'inspector' ) ) {
			return null
		}

		const loadedControls = applyFilters( 'block_lab_controls', controls );
		const controlFunction = field.controlFunction || loadedControls[ field.control ];
		const control = typeof controlFunction !== 'undefined' ? controlFunction( blockProps, field, block ) : null;

		return (
			<PanelBody key={ `inspector-controls-panel-${ field.name }` }>
				{ control }
			</PanelBody>
		)
	} )

	return (
		<InspectorControls key={ `inspector-controls${ block.name }` }>
			{ fields }
		</InspectorControls>
	)
}

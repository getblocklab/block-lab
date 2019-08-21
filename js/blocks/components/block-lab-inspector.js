/**
 * WordPress dependencies
 */
const { PanelBody } = wp.components;
const { InspectorControls } = wp.editor;
const { applyFilters } = wp.hooks;

/**
 * Internal dependencies
 */
import simplifiedFields from '../loader/fields';
import controls from '../controls';

/**
 * Gets the rendered controls for the Inspector Controls, based on the field values.
 *
 * @param {Object} blockProps The block's props.
 * @param {Object} block The block.
 * @return {Function|null} The inspector controls.
 */
const BlockLabInspector = ( { blockProps, block } ) => {
	const fields = simplifiedFields( block.fields ).map( field => {
		// If its not meant for the inspector then continue (return null).
		if ( ! field.location || ! field.location.includes( 'inspector' ) ) {
			return null
		}

		const loadedControls = applyFilters( 'block_lab_controls', controls );
		const Control = field.controlFunction || loadedControls[ field.control ];
		if ( ! Control ) {
			return null;
		}

		const { attributes, setAttributes } = blockProps;
		const attr = { ...attributes };

		return (
			<PanelBody key={ `inspector-controls-panel-${ field.name }` }>
				<Control
					field={ field }
					getValue={ () => {
						return attr[ field.name ];
					} }
					onChange={ ( newValue ) => {
						attr[ field.name ] = newValue;
						setAttributes( attr );
					} }
					parentBlock={ block }
					parentBlockProps={ blockProps }
				/>
			</PanelBody>
		)
	} )

	return (
		<InspectorControls key={ `inspector-controls${ block.name }` }>
			{ fields }
		</InspectorControls>
	)
}

export default BlockLabInspector;

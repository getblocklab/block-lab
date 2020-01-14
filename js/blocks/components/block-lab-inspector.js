/**
 * WordPress dependencies
 */
import { PanelBody } from '@wordpress/components';
import { InspectorControls } from '@wordpress/block-editor';
import { applyFilters } from '@wordpress/hooks';

/**
 * Internal dependencies
 */
import { getSimplifiedFields } from '../helpers';
import controls from '../controls';

/**
 * Gets the rendered controls for the Inspector Controls, based on the field values.
 *
 * @param {Object} props This component's props.
 * @param {Object} props.blockProps The block's props.
 * @param {Object} props.block The block.
 * @return {Function|null} The inspector controls.
 */
const BlockLabInspector = ( { blockProps, block } ) => {
	const fields = getSimplifiedFields( block.fields ).map( ( field ) => {
		// If it's not meant for the inspector, continue (return null).
		if ( ! field.location || ! field.location.includes( 'inspector' ) ) {
			return null;
		}

		const loadedControls = applyFilters( 'block_lab_controls', controls );
		const Control = loadedControls[ field.control ];
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
		);
	} );

	return (
		<InspectorControls key={ `inspector-controls${ block.name }` }>
			{ fields }
		</InspectorControls>
	);
};

export default BlockLabInspector;

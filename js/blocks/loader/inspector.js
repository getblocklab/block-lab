import { simplifiedFields } from "./fields";
import controls from "../controls";

const { InspectorControls } = wp.editor;
const { PanelBody } = wp.components;
const { applyFilters } = wp.hooks;

const inspectorControls = ( props, block ) => {

	const fields = simplifiedFields( block.fields ).map( field => {

		// If its not meant for the inspector then continue (return null).
		if ( ! field.location || ! field.location.includes('inspector') ) {
			return null
		}

		const loadedControls = applyFilters( 'block_lab_controls', controls );
		const controlFunction = field.controlFunction || loadedControls[ field.control ];
		const control = typeof controlFunction !== 'undefined' ? controlFunction( props, field, block ) : null;

		return (
			<PanelBody key={"inspector-controls-panel-" + field}>
				{control}
			</PanelBody>
		)
	} )

	return (
		<InspectorControls key={"inspector-controls" + block.name}>
			{fields}
		</InspectorControls>
	)
}

export default inspectorControls
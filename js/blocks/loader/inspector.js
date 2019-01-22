import { simplifiedFields } from "./fields";
import controls from "../controls";

const { InspectorControls } = wp.editor;
const { PanelBody } = wp.components;


const inspectorControls = ( props, block ) => {

	const fields = simplifiedFields( block.fields ).map( field => {

		// If its not meant for the inspector then continue (return null).
		if ( ! field.location || ! field.location.includes('inspector') ) {
			return null
		}

		const controlFunction = field.controlFunction || controls[ field.control ];
		const control = typeof controlFunction !== 'undefined' ? controlFunction( props, field, block ) : null;

		return (
			<PanelBody>
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
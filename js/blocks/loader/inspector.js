import { getControl } from './controls'
import { simplifiedFields } from "./fields";

'./fields'

const {
	InspectorControls,
} = wp.editor;
const {
	CheckboxControl,
	PanelBody,
	PanelRow,
	PanelColor,
	RadioControl,
	RangeControl,
	TextControl,
	TextareaControl,
	ToggleControl,
	SelectControl
} = wp.components;


const inspectorControls = ( props, block ) => {

	const fields = simplifiedFields( block.fields ).map( field => {

		// If its not meant for the inspector then continue (return null).
		if ( ! field.location || ! field.location.includes('inspector') ) {
			return null
		}

		return (
			<PanelBody>
				{getControl( props, field, block )}
			</PanelBody>
		)
	} )

	return (
		<InspectorControls>
			{fields}
		</InspectorControls>
	)
}

export default inspectorControls
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

const compare = ( a, b ) => {
	if ( a.order < b.order )
		return -1;
	if ( a.order > b.order )
		return 1;
	return 0;
}

const simplifiedFields = ( fields ) => {

	let fieldList = []

	for ( let fieldName in fields ) {

		if ( !fields.hasOwnProperty( fieldName ) ) continue;

		let field = fields[ fieldName ];

		fieldList.push(
			{
				...field,
				name: fieldName,
			}
		)
	}

	fieldList.sort(compare)

	return fieldList
}

const inspectorControls = block => {

	const fields = simplifiedFields( block.fields ).map( field => {

		// If its not meant for the inspector then continue (return null).
		if ( ! field.location || ! field.location.includes('inspector') ) {
			return null
		}

		let control;

		switch ( field.control ) {
			case 'text':
				control = (
					<TextControl
						label={ field.label }
						help=''
						value={ field.default || '' }
						onChange={ textControl => {
							const attr = {
								...field.name
							}
							attr[ field.name ] = textControl
							setAttributes( attr )
						} }
					/>
				)
				break;
			case 'textarea':
				control = (
					<TextareaControl
						label={ field.label }
						help=''
						value={ field.default || '' }
						onChange={ textControl => {
							const attr = {
								...field.name
							}
							attr[ field.name ] = textControl
							setAttributes( attr )
						} }
					/>
				)
				break;
			default: control = (
				<p>Field { field.name }</p>
			)
		} 

		return (
			<PanelBody>
				{ control }
			</PanelBody>
		)
	} )

	return (
		<InspectorControls>
			{ fields }
		</InspectorControls>
	)
}

export default inspectorControls
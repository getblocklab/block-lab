const updatePreview = (props, block, data) => {
	for ( let attrKey in block.fields ) {

		if ( !block.fields.hasOwnProperty( attrKey ) ) continue;

		data = data || props.attributes.block_template

		let attr = props.attributes[ attrKey ];
		let value = typeof attr != 'undefined' ? attr : '';

		data = data.replace( '<"' + attrKey + '">', value )
	}

	props.setAttributes( { block_preview: data } )
}

export default updatePreview

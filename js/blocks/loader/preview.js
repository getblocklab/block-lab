const updatePreview = (props, block, data) => {
	for ( let attrKey in block.fields ) {

		if ( !block.fields.hasOwnProperty( attrKey ) ) continue;

		data = data || props.attributes.block_template || ''

		if ( 'string' !== typeof data ) {
			data = ''
		}

		let attr   = props.attributes[ attrKey ];
		let value  = typeof attr != 'undefined' ? attr : '';
		let search = new RegExp( '\\["' + attrKey + '"\\]', 'g' );

		data = data.replace( search, value )
	}

	props.setAttributes( { block_preview: data } )
}

export default updatePreview

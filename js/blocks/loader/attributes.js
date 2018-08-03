const getBlockAttributes = block => {

	let attributes = {}

	for ( let fieldName in block.fields ) {

		if ( !block.fields.hasOwnProperty( fieldName ) ) continue;

		let field = block.fields[ fieldName ];

		attributes[ fieldName ] = {}

		if ( field.type ) {
			attributes[ fieldName ].type = field.type
		}

		if ( field.source ) {
			attributes[ fieldName ].source = field.source
		}

		if ( field.meta ) {
			attributes[ fieldName ].meta = field.meta
		}

		if ( field.default ) {
			attributes[ fieldName ].default = field.default
		}

		if ( field.selector ) {
			attributes[ fieldName ].selector = field.selector
		}

		if ( field.query ) {
			attributes[ fieldName ].query = field.query
		}
	}

	attributes[ 'block_template' ] = {
		type: 'string',
		source: 'children'
	};

	attributes[ 'block_preview' ] = {
		type: 'string',
		source: 'children'
	};

	return attributes
};

export default getBlockAttributes
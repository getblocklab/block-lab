const getBlockAttributes = block => {

	let attributes = {}

	for ( let fieldName in block.fields ) {

		if ( !block.fields.hasOwnProperty( fieldName ) ) continue;

		let field = block.fields[ fieldName ];

		attributes[ fieldName ] = {}

		if ( field.type ) {
			attributes[ fieldName ].type = field.type
		}

		if ( field.default ) {
			attributes[ fieldName ].default = field.default
		}

	}

	return attributes
};

export default getBlockAttributes
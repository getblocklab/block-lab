/**
 * Gets the attributes for a block, based on given fields.
 *
 * @param {Object} fields     The fields to get the attributes from.
 * @return {Object} attributes The attributes for the fields.
 */
const getBlockAttributes = ( fields ) => {

	let attributes = {};

	for ( let fieldName in fields ) {

		if ( ! fields.hasOwnProperty( fieldName ) ) {
			continue;
		}

		let field = fields[ fieldName ];

		attributes[ fieldName ] = {};

		if ( field.type ) {
			attributes[ fieldName ].type = field.type
		}

		if ( field.default ) {
			attributes[ fieldName ].default = field.default;
		}
	}

	return attributes;
};

export default getBlockAttributes;

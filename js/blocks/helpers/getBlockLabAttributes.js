/**
 * Gets the attributes for a block, based on given fields.
 *
 * @param {Object} fields The fields to get the attributes from.
 * @return {Object} attributes The attributes for the fields.
 */
const getBlockLabAttributes = ( fields ) => {
	const attributes = {};

	for ( const fieldName in fields ) {
		if ( ! fields.hasOwnProperty( fieldName ) ) {
			continue;
		}

		const field = fields[ fieldName ];
		attributes[ fieldName ] = {};

		if ( field.type ) {
			attributes[ fieldName ].type = field.type;
		}

		if ( field.default ) {
			attributes[ fieldName ].default = field.default;
		}
	}

	return attributes;
};

export default getBlockLabAttributes;

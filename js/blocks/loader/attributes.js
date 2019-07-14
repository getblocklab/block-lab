/**
 * Gets the attributes for a block, based on given fields.
 *
 * @param {Object} fields     The fields to get the attributes from.
 * @return {Array} attributes The attributes for the fields.
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

		// Account for 'sub_fields' in the Repeater control.
		if ( field.hasOwnProperty( 'sub_fields' ) ) {
			let subFields = getBlockAttributes( field.sub_fields );
			attributes = Object.assign( {}, attributes, subFields );
		}

	}

	return attributes;
};

export default getBlockAttributes

const compare = ( a, b ) => {
	if ( a.order < b.order )
		return -1;
	if ( a.order > b.order )
		return 1;
	return 0;
}

/**
 * Gets a simplified and sorted array of the fields.
 *
 * @param {Array}  fields  The fields to simplify.
 * @param {String} rowName The name of a repeater row the fields are in, if any (optional).
 * @retun {Array} The simplified fields.
 */
const simplifiedFields = ( fields, rowName = null ) => {

	let fieldList = []

	for ( let fieldName in fields ) {
		if ( '' === fieldName ) {
			continue;
		}

		if ( ! fields.hasOwnProperty( fieldName ) ) {
			continue;
		}

		let field = fields[ fieldName ];
		fieldList.push(
			{
				...field,
				name: rowName ? `${ rowName }[${ fieldName }]` : fieldName, // If in a repeater row, include the row in the name.
			}
		)
	}

	fieldList.sort( compare );

	return fieldList
}

export default simplifiedFields;

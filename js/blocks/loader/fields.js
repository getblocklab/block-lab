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
 * @param {number} rowIndex The index of the repeater row.
 * @retun {Array} The simplified fields.
 */
const simplifiedFields = ( fields, rowIndex = null ) => {

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
				name: fieldName,
			}
		)
	}

	fieldList.sort( compare );

	return fieldList
}

export default simplifiedFields;

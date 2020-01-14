/**
 * Gets the comparison between two objects.
 *
 * @param {Object} a The first to compare.
 * @param {Object} b The second to compare.
 * @return {number} Either -1, 0, or 1, depending on the comparison.
 */
const compare = ( a, b ) => {
	if ( a.order < b.order ) {
		return -1;
	}
	if ( a.order > b.order ) {
		return 1;
	}
	return 0;
};

/**
 * Gets a simplified and sorted array of the fields.
 *
 * @param {Array} fields The fields to simplify.
 * @return {Array} The simplified fields.
 */
const getSimplifiedFields = ( fields ) => {
	const fieldList = [];

	for ( const fieldName in fields ) {
		if ( '' === fieldName ) {
			continue;
		}

		if ( ! fields.hasOwnProperty( fieldName ) ) {
			continue;
		}

		const field = fields[ fieldName ];
		fieldList.push(
			{
				...field,
				name: fieldName,
			}
		);
	}

	fieldList.sort( compare ); // @todo: is this needed? Even then, it should only affect the Block Lab editor UI.

	return fieldList;
};

export default getSimplifiedFields;

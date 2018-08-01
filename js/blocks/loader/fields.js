const compare = ( a, b ) => {
	if ( a.order < b.order )
		return -1;
	if ( a.order > b.order )
		return 1;
	return 0;
}

const simplifiedFields = ( fields ) => {

	let fieldList = []

	for ( let fieldName in fields ) {

		if ( !fields.hasOwnProperty( fieldName ) ) continue;

		let field = fields[ fieldName ];

		fieldList.push(
			{
				...field,
				name: fieldName,
			}
		)
	}

	fieldList.sort( compare )

	return fieldList
}

export { simplifiedFields }
/**
 * Renames a field to a new slug.
 *
 * The field 'name' is also the index of the field in the fields object,
 * so this move that to the new index.
 *
 * @param {Object} block The block, including fields.
 * @param {string} previousSlug The previous slug of the field.
 * @param {string} newSlug The new slug of the field.
 */
const renameField = ( block, previousSlug, newSlug ) => {
	const previousField = block.fields[ previousSlug ];
	block.fields[ newSlug ] = previousField;
	block.fields[ newSlug ].name = newSlug;
	delete block.fields[ previousSlug ];

	return block;
};

export default renameField;

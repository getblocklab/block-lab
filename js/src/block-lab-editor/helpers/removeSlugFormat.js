/**
 * Converts a slug into text without the '-', in Title Case.
 *
 * For example, 'new-field-4' to 'New Field 4'.
 *
 * @param {string} text The text to de-slugify.
 * @return {string} The de-slugified text.
 */
const removeSlugFormat = ( text ) => {
	return text
		.split( '-' )
		.map( ( word ) => word.charAt( 0 ).toUpperCase() + word.slice( 1 ) )
		.join( ' ' );
};

export default removeSlugFormat;

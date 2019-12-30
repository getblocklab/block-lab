/**
 * Converts text into a slug.
 *
 * For example, 'Foo Block' to 'foo-block'.
 *
 * @param {string} text The text to slugify.
 * @return {string} The slugified text.
 */
const slugify = ( text ) => {
	return text
		.toLowerCase()
		.replace( /[^\w ]+/g, '' )
		.replace( / +/g, '-' )
		.replace( /_+/g, '-' );
};

export default slugify;

/**
 * Parses the block from the post content into an object.
 *
 * @param {string} content The post content, probably containing JSON.
 * @return {Object|boolean} The block parsed into an object, or false.
 */
const getBlockFromContent = ( content ) => {
	try {
		const parsedContent = JSON.parse( content );
		const values = Object.values( parsedContent );
		return values[ 0 ] ? values[ 0 ] : false;
	} catch ( e ) {
		return false;
	}
};

export default getBlockFromContent;

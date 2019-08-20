import ContentControl from './content';

const BlockLabPostControl = ( props ) => {
	/**
	 * Gets the post title from an API response.
	 *
	 * @param {Object} apiResponse The API response in which to look for the post title.
	 * @return {String} The post title from the response, or the default.
	 */
	const getTitleFromAPI = apiResponse => ( apiResponse && apiResponse.title && apiResponse.title.rendered ) ? apiResponse.title.rendered : '';

	return <ContentControl { ...props, { getTitleFromAPI } } />;
}

export default BlockLabPostControl;

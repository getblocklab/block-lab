import Content from './content';

const BlockLabTaxonomyControl = ( props, field, block ) => {
	/**
	* Gets the taxonomy name from an API response.
	*
	* @param {Object} apiResponse The API response in which to look for the post title.
	* @return {String} The post title from the response, or the default.
	*/
	const getNameFromAPI = apiResponse => ( apiResponse && apiResponse.name ) ? apiResponse.name : '';

	return Content( props, field, getNameFromAPI );
}

export default BlockLabTaxonomyControl

/**
 * Internal dependencies
 */
import { ContentControl } from '../components';

const BlockLabPostControl = ( props ) => {
	/**
	 * Gets the post title from an API response.
	 *
	 * @param {Object} apiResponse The API response in which to look for the post title.
	 * @return {string} The post title from the response, or the default.
	 */
	const getNameFromAPI = ( apiResponse ) => ( apiResponse && apiResponse.title && apiResponse.title.rendered ) ? apiResponse.title.rendered : '';
	const contentProps = { ...props, getNameFromAPI };
	return <ContentControl { ...contentProps } />;
};

export default BlockLabPostControl;

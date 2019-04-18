import FetchInput from './fetch-input';

const BlockLabPostControl = ( props, field, block ) => {
	const { setAttributes } = props;
	const attr = { ...props.attributes };
	const DEFAULT_TITLE = '';
	const DEFAULT_ID = 0;

	/**
	* Gets the post title from an API response.
	*
	* @param {Object} apiResponse The API response in which to look for the post title.
	* @return {String} The post title from the response, or the default.
	*/
	const getTitleFromAPI = apiResponse => {
		if ( apiResponse && apiResponse.title && apiResponse.title.rendered ) {
			return apiResponse.title.rendered;
		}
		return DEFAULT_TITLE;
	}

	/**
	 * Gets the post ID from an API response.
	 *
	 * @param {Object} value The value in which to look for the ID.
	 * @return {Number} The ID from the value, or 0.
	 */
	const getIdfromAPI = apiResponse => {
		if ( apiResponse && apiResponse.id ) {
			return parseInt( apiResponse.id );
		}
		return DEFAULT_ID;
	}

	attr[ field.name ] = Object.assign( { id: DEFAULT_ID, title: DEFAULT_TITLE }, attr[ field.name ] );
	const postAttribute = attr[ field.name ];

	return (
		<FetchInput
			field={field}
			placeholder={field.placeholder}
			apiSlug={field.post_type_rest_slug}
			value={postAttribute['id'] }
			displayValue={postAttribute['title']}
			getValueFromAPI={getIdfromAPI}
			getDisplayValueFromAPI={getTitleFromAPI}
			onChange={value => {
				if ( 'string' === typeof value ) {
					// The value is probably from the user typing into the <input>.
					postAttribute['title'] = value;
					postAttribute['id'] = DEFAULT_ID;
				} else {
					// The value is probably an Object, from the user selecting a link in the Popover.
					postAttribute['id'] = getIdfromAPI( value );
					postAttribute['title'] = getTitleFromAPI( value );
				}
				attr[ field.name ] = postAttribute;
				setAttributes( attr );
			}}
		/>
	);
}

export default BlockLabPostControl

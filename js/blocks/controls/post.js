import FetchInput from './fetch-input';

const BlockLabPostControl = ( props, field, block ) => {
	const { setAttributes } = props;
	const attr = { ...props.attributes };
	const displayValueKey = field.name + '-displayValue';

	/**
	* Gets the post title from an API response.
	*
	* @param {Object} value The value in which to look for the post title.
	* @return {String} The title from the value, or ''.
	*/
	const getTitleFromAPI = apiResponse => {
		if ( apiResponse && apiResponse.title && apiResponse.title.rendered ) {
			return apiResponse.title.rendered;
		}
		return '';
	}

	/**
	 * Gets the post ID from an API response.
	 *
	 * @param {Object} value The value in which to look for the ID.
	 * @return {Number} The ID from the value, or 0.
	 */
	const getIdfromAPI = value => {
		if ( value && value.id ) {
			return parseInt( value.id );
		}
		return 0;
	}

	return (
		<FetchInput
			field={field}
			placeholder={field.placeholder}
			value={attr[ field.name ]}
			displayValue={attr[ displayValueKey ]}
			apiSlug="posts"
			getValueFromAPI={getIdfromAPI}
			getDisplayValueFromAPI={getTitleFromAPI}
			onChange={value => {
				if ( 'string' === typeof value ) {
					// The value was probably from the user typing into the <input>.
					attr[ displayValueKey ] = value;
				} else {
					// The value is probably an Object, from the user selecting a link in the popover.
					attr[ field.name ] = getIdfromAPI( value );
					attr[ displayValueKey ] = getTitleFromAPI( value );
				}
				setAttributes( attr );
			}}
		/>
	);
}

export default BlockLabPostControl

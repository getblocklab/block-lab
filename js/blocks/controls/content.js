import FetchInput from './fetch-input';

/**
 * Gets a content control, eg. a Post or Taxonomy control.
 *
 * @return {Function} A component for a control.
 */
export default ( props, field, getNameFromAPI ) => {
	const { setAttributes } = props;
	const attr = { ...props.attributes };
	const DEFAULT_ID = 0;
	const DEFAULT_NAME = '';

	/**
	 * Gets the ID from an API response.
	 *
	 * @param {Object} value The value in which to look for the ID.
	 * @return {Number} The ID from the value, or 0.
	 */
	const getIdfromAPI = apiResponse => ( apiResponse && apiResponse.id ) ? parseInt( apiResponse.id ) : DEFAULT_ID;

	attr[ field.name ] = Object.assign( { id: DEFAULT_ID, name: DEFAULT_NAME }, attr[ field.name ] );
	const valueAttribute = attr[ field.name ];

	return (
		<FetchInput
			field={field}
			apiSlug={field.post_type_rest_slug}
			value={valueAttribute['id']}
			displayValue={valueAttribute['name']}
			getValueFromAPI={getIdfromAPI}
			getDisplayValueFromAPI={getNameFromAPI}
			onChange={value => {
				if ( 'string' === typeof value ) {
					// The value is probably from the user typing into the <input>.
					valueAttribute['name'] = value;
					valueAttribute['id'] = DEFAULT_ID;
				} else {
					// The value is probably an Object, from the user selecting a link in the Popover.
					valueAttribute['name'] = getNameFromAPI( value );
					valueAttribute['id'] = getIdfromAPI( value );
				}
				attr[ field.name ] = valueAttribute;
				setAttributes( attr );
			}}
		/>
	);
}

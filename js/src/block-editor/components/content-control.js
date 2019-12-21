/**
 * Internal dependencies
 */
import { FetchInput } from '../components';

/**
 * Gets a content control, eg. a Post or Taxonomy control.
 *
 * @param {Object} props The props of the control.
 * @return {Function} A component for a control.
 */
const ContentControl = ( props ) => {
	const { field, getValue, getNameFromAPI, onChange } = props;
	const DEFAULT_ID = 0;
	const DEFAULT_NAME = '';

	/**
	 * Gets the ID from an API response.
	 *
	 * @param {Object} apiResponse The API response in which to look for the ID.
	 * @return {number} The ID from the value, or 0.
	 */
	const getIdfromAPI = ( apiResponse ) => ( apiResponse && apiResponse.id ) ? parseInt( apiResponse.id ) : DEFAULT_ID;

	const initialValue = getValue( props );
	const valueAttribute = { id: DEFAULT_ID, name: DEFAULT_NAME, ...initialValue };

	return (
		<FetchInput
			field={ field }
			apiSlug={ field.post_type_rest_slug }
			value={ valueAttribute.id }
			displayValue={ valueAttribute.name }
			getValueFromAPI={ getIdfromAPI }
			getDisplayValueFromAPI={ getNameFromAPI }
			onChange={ ( value ) => {
				if ( 'string' === typeof value ) {
					// The value is probably from the user typing into the <input>.
					valueAttribute.name = value;
					valueAttribute.id = DEFAULT_ID;
				} else {
					// The value is probably an Object, from the user selecting a link in the Popover.
					valueAttribute.name = getNameFromAPI( value );
					valueAttribute.id = getIdfromAPI( value );
				}

				onChange( valueAttribute );
			} }
		/>
	);
};

export default ContentControl;

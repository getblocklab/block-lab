/**
 * Internal dependencies
 */
import { FetchInput } from '../components';

const BlockLabUserControl = ( props ) => {
	const { field, getValue, onChange } = props;
	const DEFAULT_ID = 0;
	const getIdFromAPI = ( apiResponse ) => ( apiResponse && apiResponse.id ) ? apiResponse.id : DEFAULT_ID;
	const getNameFromAPI = ( apiResponse ) => ( apiResponse && apiResponse.name ) ? apiResponse.name : '';

	const initialValue = ( 'object' === typeof getValue( props ) ) ? getValue( props ) : {};
	const userAttribute = { id: DEFAULT_ID, userName: '', ...initialValue };

	return (
		<FetchInput
			field={ field }
			apiSlug="users"
			value={ userAttribute.id }
			displayValue={ userAttribute.userName }
			getValueFromAPI={ getIdFromAPI }
			getDisplayValueFromAPI={ getNameFromAPI }
			onChange={ ( value ) => {
				if ( 'string' === typeof value ) {
					// The value is probably from the user typing into the <input>.
					userAttribute.userName = value;
					userAttribute.id = DEFAULT_ID;
				} else {
					// The value is probably an Object, from the user selecting a link in the Popover.
					userAttribute.userName = getNameFromAPI( value );
					userAttribute.id = getIdFromAPI( value );
				}

				onChange( userAttribute );
			} }
		/>
	);
};

export default BlockLabUserControl;

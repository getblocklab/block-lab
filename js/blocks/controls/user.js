import FetchInput from './fetch-input';

const BlockLabUserControl = ( props, field, block ) => {
	const { setAttributes } = props;
	const attr = { ...props.attributes };
	const DEFAULT_ID = 0;
	const getIdFromAPI = apiResponse => ( apiResponse && apiResponse.id ) ? apiResponse.id : DEFAULT_ID;
	const getNameFromAPI = apiResponse => ( apiResponse && apiResponse.name ) ? apiResponse.name : '';

	const initialValue = ( 'object' === typeof attr[ field.name ] ) ? attr[ field.name ] : {};
	attr[ field.name ] = Object.assign( { id: DEFAULT_ID, userName: '' }, initialValue );
	const userAttribute = attr[ field.name ];

	return (
		<FetchInput
			field={field}
			placeholder={field.placeholder}
			apiSlug="users"
			value={userAttribute['id']}
			displayValue={userAttribute['userName']}
			getValueFromAPI={getIdFromAPI}
			getDisplayValueFromAPI={getNameFromAPI}
			onChange={value => {
				if ( 'string' === typeof value ) {
					// The value is probably from the user typing into the <input>.
					userAttribute['userName'] = value;
					userAttribute['id'] = DEFAULT_ID;
				} else {
					// The value is probably an Object, from the user selecting a link in the Popover.
					userAttribute['userName'] = getNameFromAPI( value );
					userAttribute['id'] = getIdFromAPI( value );
				}
				attr[ field.name ] = userAttribute;
				setAttributes( attr );
			}}
		/>
	);
}

export default BlockLabUserControl

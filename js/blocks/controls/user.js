import FetchInput from './fetch-input';

const BlockLabUserControl = ( props, field, block ) => {
	const { setAttributes } = props;
	const attr = { ...props.attributes };
	const getValueFromAPI = apiResponse => apiResponse.slug ? apiResponse.slug : apiResponse;

	return (
		<FetchInput
			field={field}
			apiSlug="users"
			value={attr[ field.name ]}
			getValueFromAPI={getValueFromAPI}
			onChange={value => {
				attr[ field.name ] = getValueFromAPI( value )
				setAttributes( attr )
			}}
		/>
	);
}

export default BlockLabUserControl

import FetchInput from './fetch-input';

const BlockLabUserControl = ( props, field, block ) => {
	const { setAttributes, className } = props;
	const attr = { ...props.attributes };
	const getValueFromAPI = apiResponse => apiResponse.slug;

	return (
		<FetchInput
			field={field}
			placeholder={field.placeholder}
			value={attr[ field.name ]}
			apiSlug="users"
			getValueFromAPI={getValueFromAPI}
			onChange={value => {
				attr[ field.name ] = getValueFromAPI( value )
				setAttributes( attr )
			}}
		/>
	);
}

export default BlockLabUserControl

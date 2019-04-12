import FetchInput from './fetch-input';

const BlockLabPostControl = ( props, field, block ) => {
	const { setAttributes } = props;
	const attr = { ...props.attributes };
	const displayValueKey = field.name + '-displayValue';

	const getTitleFromAPI = ( apiResponse ) => {
		if ( apiResponse && apiResponse.title && apiResponse.title.rendered ) {
			return apiResponse.title.rendered;
		}
		return '';
	}

	const getIdfromAPI = ( value ) => {
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
			displayValue={attr[ displayValueKey ] || ''}
			apiSlug="posts"
			getValueFromAPI={getIdfromAPI}
			getDisplayValue={getTitleFromAPI}
			onChange={value => {
				attr[field.name] = getIdfromAPI( value )
				attr[displayValueKey] = getTitleFromAPI( value )
				setAttributes( attr )
			}}
		/>
	);
}

export default BlockLabPostControl

import FetchInput from './fetch-input';

const BlockLabUserControl = ( props, field, block ) => {
	const { setAttributes, className } = props;
	const attr = { ...props.attributes };

	return (
		<FetchInput
			field={field}
			placeholder={field.placeholder}
			value={attr[ field.name ]}
			resultKey="slug"
			apiSlug="users"
			onChange={username => {
				attr[ field.name ] = username
				setAttributes( attr )
			}}
		/>
	);
}

export default BlockLabUserControl

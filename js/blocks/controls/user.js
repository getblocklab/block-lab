import UserInput from './user-input';

const BlockLabUserControl = ( props, field, block ) => {
	const { setAttributes, className } = props;
	const attr = { ...props.attributes };

	return (
		<UserInput
			className="url"
			placeholder={ field.placeholder }
			value={attr[ field.name ]}
			onChange={username => {
				attr[ field.name ] = username
				setAttributes( attr )
			}}
		/>
	);
}

export default BlockLabUserControl

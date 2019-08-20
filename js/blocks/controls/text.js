const { TextControl } = wp.components;

const BlockLabTextControl = ( props ) => {
	const { field, getValue, onChange } = props;

	return (
		<TextControl
			label={ field.label }
			placeholder={ field.placeholder || '' }
			maxLength={ field.maxlength }
			help={ field.help }
			defaultValue={ field.default }
			value={ getValue( props ) }
			onChange={ onChange }
		/>
	)
};

export default BlockLabTextControl
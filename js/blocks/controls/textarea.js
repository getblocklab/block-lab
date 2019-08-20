const { TextareaControl } = wp.components;

const BlockLabTextareaControl = ( props ) => {
	const { getValue, field, onChange } = props

	return (
		<TextareaControl
			label={ field.label }
			placeholder={ field.placeholder || '' }
			maxLength={ field.maxlength }
			rows={ field.number_rows }
			help={ field.help }
			defaultValue={ field.default }
			value={ getValue( props ) }
			onChange={ onChange }
		/>
	);
}

export default BlockLabTextareaControl
const { TextareaControl } = wp.components;

const BlockLabTextareaControl = ( props, field, block ) => {
	const { setAttributes } = props
	const attr = { ...props.attributes }
	return (
		<TextareaControl
			label={field.label}
			placeholder={field.placeholder || ''}
			maxLength={field.maxlength}
			help={field.help}
			defaultValue={field.default}
			value={attr[ field.name ]}
			onChange={textareaControl => {
				attr[ field.name ] = textareaControl
				setAttributes( attr )
			}}
		/>
	)
}

export default BlockLabTextareaControl
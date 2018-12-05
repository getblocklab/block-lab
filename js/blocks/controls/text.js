const { TextControl } = wp.components;

const BlockLabTextControl = ( props, field, block ) => {
	const { setAttributes } = props;
	const attr = { ...props.attributes };
	return (
		<TextControl
			label={field.label}
			placeholder={field.placeholder || ''}
			maxLength={field.maxlength}
			help={field.help}
			defaultValue={field.default}
			value={attr[ field.name ]}
			onChange={textControl => {
				attr[ field.name ] = textControl
				setAttributes( attr )
			}}
		/>
	)
}

export default BlockLabTextControl
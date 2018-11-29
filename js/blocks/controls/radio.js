const { RadioControl } = wp.components;

const BlockLabRadioControl = ( props, field, block ) => {
	const { setAttributes } = props
	const attr = { ...props.attributes }
	return (
		<RadioControl
			label={field.label}
			help={field.help}
			selected={attr[ field.name ] || field.default}
			options={field.options}
			onChange={radioControl => {
				attr[ field.name ] = radioControl
				setAttributes( attr )
			}}
		/>
	)
}

export default BlockLabRadioControl
const { SelectControl } = wp.components;

const BlockLabMultiselectControl = ( props, field, block ) => {
	const { setAttributes } = props
	const attr = { ...props.attributes }

	return (
		<SelectControl
			multiple="true"
			label={field.label}
			help={field.help}
			value={attr[ field.name ] || field.default}
			options={field.items}
			onChange={multiselectControl => {
				attr[ field.name ] = multiselectControl
				setAttributes( attr )
			}}
		/>
	)
}

export default BlockLabMultiselectControl
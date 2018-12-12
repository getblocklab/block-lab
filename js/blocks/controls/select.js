const { SelectControl } = wp.components;

const BlockLabSelectControl = ( props, field, block ) => {
	const { setAttributes } = props
	const attr = { ...props.attributes }

	return (
		<SelectControl
			label={field.label}
			help={field.help}
			value={attr[ field.name ] || field.default}
			options={field.items}
			onChange={selectControl => {
				attr[ field.name ] = selectControl
				setAttributes( attr )
			}}
		/>
	)
}

export default BlockLabSelectControl
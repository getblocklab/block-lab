const { ToggleControl } = wp.components;

const BlockLabToggleControl = ( props, field, block ) => {
	const { setAttributes } = props
	const attr = { ...props.attributes }
	if ( 'undefined' === typeof attr[ field.name ] ) {
		attr[ field.name ] = field.default
	}
	return (
		<ToggleControl
			label={field.label}
			help={field.help}
			checked={attr[ field.name ]}
			onChange={toggleControl => {
				attr[ field.name ] = toggleControl
				setAttributes( attr )
			}}
		/>
	)
}

export default BlockLabToggleControl
const { SelectControl } = wp.components;

const BlockLabSelectControl = ( props, field, block ) => {
	const { setAttributes } = props
	const attr = { ...props.attributes }
	const { __ } = wp.i18n;

	if ( '' === field.default ) {
		field.options = [
			// @see https://github.com/WordPress/gutenberg/issues/11270 Disabled attribute not currently supported.
			{ label: __( '– Select –', 'block-lab' ), value: '', disabled: true },
			...field.options
		]
	}

	return (
		<SelectControl
			label={field.label}
			help={field.help}
			value={attr[ field.name ] || field.default}
			options={field.options}
			onChange={selectControl => {
				attr[ field.name ] = selectControl
				setAttributes( attr )
			}}
		/>
	)
}

export default BlockLabSelectControl
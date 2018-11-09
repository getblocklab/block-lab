import updatePreview from "../loader/preview";

const { SelectControl } = wp.components;

const BlockLabSelectControl = ( props, field, block ) => {
	const { setAttributes } = props
	const attr = { ...props.attributes }
	const multiple = field.multiple ? 'multiple' : ''

	return (
		<SelectControl
			multiple={multiple}
			label={field.label}
			help={field.help}
			value={attr[ field.name ] || field.default}
			options={field.options}
			onChange={selectControl => {
				attr[ field.name ] = selectControl
				setAttributes( attr )
			}}
			onBlur={
				updatePreview( props, block )
			}
		/>
	)
}

export default BlockLabSelectControl
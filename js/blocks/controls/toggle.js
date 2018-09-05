import updatePreview from "../loader/preview";

const { ToggleControl } = wp.components;

const BLToggleControl = ( props, field, block ) => {
	const { setAttributes } = props
	const attr = { ...props.attributes }
	return (
		<ToggleControl
			label={field.label}
			help={field.help}
			checked={attr[ field.name ] || field.default}
			onChange={toggleControl => {
				attr[ field.name ] = toggleControl
				setAttributes( attr )
			}}
			onBlur={
				updatePreview( props, block )
			}
		/>
	)
}

export default BLToggleControl
import updatePreview from "../loader/preview";
const { ToggleControl } = wp.components;

const ACBToggleControl = (props, field, block) => {
	const { setAttributes } = props
	const attr = { ...props.attributes }
	return (
		<ToggleControl
			label={field.label}
			checked={attr[ field.name ]}
			onChange={ toggleControl => {
				attr[ field.name ] = toggleControl
				setAttributes( attr )
			} }
			onBlur={
				updatePreview( props, block )
			}
		/>
	)
}

export default ACBToggleControl
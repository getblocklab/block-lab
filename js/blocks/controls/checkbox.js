import updatePreview from "../loader/preview";

const { CheckboxControl } = wp.components;

const BlockLabCheckboxControl = ( props, field, block ) => {
	const { setAttributes } = props
	const attr = { ...props.attributes }
	if ( 'undefined' === typeof attr[ field.name ] ) {
		attr[ field.name ] = field.default || false
	}
	return (
		<CheckboxControl
			label={field.label}
			help={field.help}
			checked={attr[ field.name ]}
			options={field.options}
			onChange={checkboxControl => {
				attr[ field.name ] = checkboxControl
				setAttributes( attr )
			}}
			onBlur={
				updatePreview( props, block )
			}
		/>
	)
}

export default BlockLabCheckboxControl
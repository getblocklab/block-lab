import updatePreview from "../loader/preview";

const { TextareaControl } = wp.components;

const ACBTextareaControl = ( props, field, block ) => {
	const { setAttributes } = props
	const attr = { ...props.attributes }
	return (
		<TextareaControl
			label={field.label}
			placeholder={field.placeholder || ''}
			maxLength={field.maxlength}
			help={field.help}
			required={field.required || false}
			value={attr[ field.name ] || field.default}
			onChange={textControl => {
				attr[ field.name ] = textControl
				setAttributes( attr )
			}}
			onKeyUp={() => {
				updatePreview( props, block )
			}}
		/>
	)
}

export default ACBTextareaControl
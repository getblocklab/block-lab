import updatePreview from "../loader/preview";

const { TextareaControl } = wp.components;

const BlockLabTextareaControl = ( props, field, block ) => {
	const { setAttributes } = props
	const attr = { ...props.attributes }
	return (
		<TextareaControl
			label={field.label}
			placeholder={field.placeholder || ''}
			maxLength={field.maxlength}
			help={field.help}
			required={field.required || false}
			defaultValue={field.default}
			value={attr[ field.name ]}
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

export default BlockLabTextareaControl
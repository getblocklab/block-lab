import updatePreview from "../loader/preview";
const { TextControl } = wp.components;

const ACBTextControl = (props, field, block) => {
	const { setAttributes } = props
	const attr = { ...props.attributes }
	return (
		<TextControl
			label={field.label}
			help=''
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

export default ACBTextControl
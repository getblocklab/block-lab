const { BaseControl } = wp.components;
const { RichText } = wp.editor;

const BlockLabRichTextControl = ( props, field, block ) => {
	const { setAttributes } = props
	const attr = { ...props.attributes }

	return (
		<BaseControl label={field.label} className="block-lab-rich-text-control " help={field.help}>
			<RichText
				placeholder={field.placeholder || ''}
				defaultValue={field.default}
				value={attr[ field.name ]}
				className='input-control'
				multiline={!!field.multiline}
				onChange={richTextControl => {
					attr[ field.name ] = richTextControl
					setAttributes( attr )
				}}
			/>
		</BaseControl>
	)
}

export default BlockLabRichTextControl
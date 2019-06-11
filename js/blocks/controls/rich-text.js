const { BaseControl } = wp.components;
const { RichText } = wp.blockEditor;
const ALIGNMENT_CONTROL_NAME = 'block-lab/rich-text-alignment';
const FORMATTING_CONTROLS = [ ALIGNMENT_CONTROL_NAME, 'bold', 'italic', 'strikethrough', 'link' ];

const BlockLabRichTextControl = ( props, field, block ) => {
	const { setAttributes } = props
	const attr = { ...props.attributes }

	return (
		<BaseControl label={field.label} className="block-lab-rich-text-control " help={field.help}>
			{
			/*
			* @todo: Resolve known issue with toolbar not disappearing on blur
			* @see: https://github.com/WordPress/gutenberg/issues/7463
			*/
			}
			<RichText
				key={ `block-lab-${ field.name }` }
				placeholder={field.placeholder || ''}
				keepPlaceholderOnFocus={true}
				defaultValue={field.default}
				value={attr[ field.name ]}
				className='input-control'
				multiline={true}
				inlineToolbar={true}
				formattingControls={ FORMATTING_CONTROLS }
				onChange={richTextControl => {
					attr[field.name] = richTextControl
					setAttributes(attr)
				}}
			/>
		</BaseControl>
	)
}

export default BlockLabRichTextControl

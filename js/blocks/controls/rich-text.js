const { BaseControl } = wp.components;
const { RichText, RichTextToolbarButton } = wp.editor;
const { Fragment } = wp.element;
const { __ } = wp.i18n;
const leftAlignmentControl = 'block-lab/left-alignment';
const centerAlignmentControl = 'block-lab/center-alignment';
const rightAlignmentControl = 'block-lab/right-alignment';
const formattingControls = [ leftAlignmentControl, centerAlignmentControl, rightAlignmentControl, 'bold', 'italic', 'strikethrough', 'link' ];

const BlockLabRichTextControl = ( props, field, block ) => {
	const { setAttributes } = props
	const attr = { ...props.attributes }

	return (
		<Fragment>
			<RichTextToolbarButton
				title={ __( 'Align Left', 'block-lab' ) }
				name={ leftAlignmentControl }
				icon="editor-alignleft"
				isActive={ false }
			/>
			<RichTextToolbarButton
				title={ __( 'Align Center', 'block-lab' ) }
				name={ centerAlignmentControl }
				icon="editor-aligncenter"
				isActive={ false }
			/>
			<RichTextToolbarButton
				title={ __( 'Align Right', 'block-lab' ) }
				name={ rightAlignmentControl }
				icon="editor-alignright"
				isActive={ false }
			/>
			<BaseControl label={field.label} className="block-lab-rich-text-control " help={field.help}>
				{
				/*
				* @todo: Resolve known issue with toolbar not disappearing on blur
				* @see: https://github.com/WordPress/gutenberg/issues/7463
				*/
				}
				<RichText
					placeholder={field.placeholder || ''}
					keepPlaceholderOnFocus={true}
					defaultValue={field.default}
					value={attr[ field.name ]}
					className='input-control'
					multiline={!!field.multiline}
					inlineToolbar={true}
					formattingControls={formattingControls}
					onChange={richTextControl => {
						attr[ field.name ] = richTextControl
						setAttributes( attr )
					}}
				/>
			</BaseControl>
		</Fragment>
	)
}

export default BlockLabRichTextControl
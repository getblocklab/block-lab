const { BaseControl } = wp.components;
const { RichText, RichTextToolbarButton } = wp.editor;
const { registerFormatType, removeFormat, toggleFormat } = wp.richText;
const { __ } = wp.i18n;
const leftAlignmentControl = 'block-lab/left-alignment';
const centerAlignmentControl = 'block-lab/center-alignment';
const rightAlignmentControl = 'block-lab/right-alignment';
const formattingControls = [ leftAlignmentControl, centerAlignmentControl, rightAlignmentControl, 'bold', 'italic', 'strikethrough', 'link' ];

registerFormatType(
	leftAlignmentControl,
	{
		title: __( 'Align Left', 'block-lab' ),
		tagName: 'p',
		className: 'bl-align-left',
		attributes: {
			align: 'style',
		},
		edit: ( props ) => {
			return (
				<RichTextToolbarButton
					title={ __( 'Align Left', 'block-lab' ) }
					name={ leftAlignmentControl }
					icon="editor-alignleft"
					isActive={ props.isActive }
					onClick={ () => {
						props.onChange(
							toggleFormat(
								props.value,
								{
									type: leftAlignmentControl,
									attributes: {
										align: 'text-align: left;',
									}
								},
							)
						);
					} }
				/>
			);
		}
	}
);

registerFormatType(
	centerAlignmentControl,
	{
		title: __( 'Align Center', 'block-lab' ),
		tagName: 'p',
		className: 'bl-align-center',
		attributes: {
			align: 'style',
		},
		edit: ( props ) => {
			return (
				<RichTextToolbarButton
					title={ __( 'Align Center', 'block-lab' ) }
					name={ centerAlignmentControl }
					icon="editor-aligncenter"
					isActive={ props.isActive }
					onClick={ () => {
						props.onChange(
							toggleFormat(
								props.value,
								{
									type: centerAlignmentControl,
									attributes: {
										align: 'text-align: center;',
									}
								},
							)
						);
					} }
				/>
			);
		}
	}
);

registerFormatType(
	rightAlignmentControl,
	{
		title: __( 'Align Right', 'block-lab' ),
		tagName: 'p',
		className: 'bl-align-right',
		attributes: {
			align: 'style',
		},
		edit: ( props ) => {
			return (
				<RichTextToolbarButton
					title={ __( 'Align Right', 'block-lab' ) }
					name={ rightAlignmentControl }
					icon="editor-alignright"
					isActive={ props.isActive }
					onClick={ () => {
						props.onChange(
							toggleFormat(
								props.value,
								{
									type: rightAlignmentControl,
									attributes: {
										align: 'text-align: right;',
									}
								},
							)
						);
					} }
				/>
			);
		}
	}
);

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
				placeholder={field.placeholder || ''}
				keepPlaceholderOnFocus={true}
				defaultValue={field.default}
				value={attr[ field.name ]}
				className='input-control'
				multiline="true"
				inlineToolbar={true}
				formattingControls={formattingControls}
				onChange={richTextControl => {
					attr[field.name] = richTextControl
					setAttributes(attr)
				}}
			/>
		</BaseControl>
	)
}

export default BlockLabRichTextControl
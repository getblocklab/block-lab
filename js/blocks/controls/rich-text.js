const { BaseControl, Fill } = wp.components;
const { RichText } = wp.editor;
const { applyFormat, registerFormatType } = wp.richText;
const { __ } = wp.i18n;
const { AlignmentToolbar } = wp.editor;
const centerAlignmentControl = 'block-lab/center-alignment';
const formattingControls = [ centerAlignmentControl, 'bold', 'italic', 'strikethrough', 'link' ];

/**
 * Gets the styling for a given alignment.
 *
 * @pram {String} alignment The alignment, like 'left' or 'right'.
 * @return {String} The alignment style, like text-align: left;.
 */
const getAlignmentStyle = ( alignment ) => {
	return `text-align: ${ alignment };`
}

const getAlignmentFromProps = ( alignmentProps ) => {
	if ( ! alignmentProps.activeAttributes || ! alignmentProps.activeAttributes.align ) {
		return;
	}

	const alignmentStyle = alignmentProps.activeAttributes.align;
	const matchedAlignments = [ 'left', 'center', 'right' ].filter( ( possibleAlignment ) => {
		return getAlignmentStyle( possibleAlignment ) === alignmentStyle;
	} );

	return matchedAlignments.length ? matchedAlignments[ 0 ] : null;
};

registerFormatType(
	centerAlignmentControl,
	{
		title: __( 'Align Center', 'block-lab' ),
		tagName: 'div',
		className: 'bl-aligned',
		attributes: {
			align: 'style',
		},
		edit: ( props ) => {
			const fillName = `RichText.ToolbarControls.${ centerAlignmentControl }`;
			return (
				<Fill name={ fillName }>
					<AlignmentToolbar
						value={ getAlignmentFromProps( props ) || '' }
						onChange={ ( newAlignment ) => {
							if ( newAlignment ) {
								props.onChange(
									applyFormat(
										props.value,
										{
											type: centerAlignmentControl,
											attributes: {
												align: getAlignmentStyle( newAlignment ),
											}
										},
									)
								);
							}
						}}
					/>
				</Fill>
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
				multiline={false}
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
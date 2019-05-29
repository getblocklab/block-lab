const { BaseControl, Fill } = wp.components;
const { RichText } = wp.editor;
const { applyFormat, registerFormatType } = wp.richText;
const { __ } = wp.i18n;
const { AlignmentToolbar } = wp.editor;
const ALIGNMENTS = [ 'left', 'center', 'right' ];
const ALIGNMENT_CONTROL_NAME = 'block-lab/rich-text-alignment';
const FORMATTING_CONTROLS = [ ALIGNMENT_CONTROL_NAME, 'bold', 'italic', 'strikethrough', 'link' ];

/**
 * Gets the styling for a given alignment.
 *
 * @pram {String} alignment The alignment, like 'left' or 'right'.
 * @return {String} The alignment style, like text-align: left;.
 */
const getAlignmentStyle = ( alignment ) => {
	return `text-align: ${ alignment };`
}

/**
 * Gets the alignment type from the properties.
 *
 * @param {Object} alignmentProps The properties for the alignment.
 * @return {String|null} The alignment, either 'left', 'center', or 'right'.
 */
const getAlignmentFromProps = ( alignmentProps ) => {
	if ( ! alignmentProps.activeAttributes || ! alignmentProps.activeAttributes.align ) {
		return;
	}

	const alignmentStyle = alignmentProps.activeAttributes.align;
	const matchedAlignments = ALIGNMENTS.filter( ( possibleAlignment ) => {
		return getAlignmentStyle( possibleAlignment ) === alignmentStyle;
	} );

	return matchedAlignments.length ? matchedAlignments[ 0 ] : null;
};

registerFormatType(
	ALIGNMENT_CONTROL_NAME,
	{
		title: __( 'Align Center', 'block-lab' ),
		tagName: 'div',
		className: 'bl-aligned',
		attributes: {
			align: 'style',
		},
		edit: ( props ) => {
			const fillName = `RichText.ToolbarControls.${ ALIGNMENT_CONTROL_NAME }`;

			return (
				<Fill name={ fillName }>
					<AlignmentToolbar
						value={ getAlignmentFromProps( props ) }
						onChange={ ( newAlignment ) => {
							if ( newAlignment ) {
								props.onChange(
									applyFormat(
										props.value,
										{
											type: ALIGNMENT_CONTROL_NAME,
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

import BlockLabRichText from './components/rich-text';

const { BaseControl, Fill } = wp.components;
const { applyFormat, registerFormatType, toggleFormat } = wp.richText;
const { __ } = wp.i18n;
const { AlignmentToolbar } = wp.editor;
const ALIGNMENTS = [ 'left', 'center', 'right' ];
const ALIGNMENT_CONTROL_NAME = 'block-lab/rich-text-alignment';
const FORMATTING_CONTROLS = [ ALIGNMENT_CONTROL_NAME, 'bold', 'italic', 'strikethrough', 'link' ];
const LINE_SEPARATOR_PATTERN = /\u2028/;

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

/**
 * Handler for the AlignmentToolbar value changing.
 *
 * @param {String} newAlignment The new alignment, like 'left' or 'center'.
 * @param {Object} props The properties of the AlignmentToolbar component.
  */
const onChangeAlign = ( newAlignment, props ) => {
	const { text } = props.value;
	const value = getAlignmentFromProps( props );
	let { start, end } = props.value;

	// If there's no text selection, only a cursor placement, align the entire string on the line where the cursor is.
	if ( start === end ) {
		while ( text.charAt( start - 1 ) && ! text.charAt( start - 1 ).match( LINE_SEPARATOR_PATTERN ) ) {
			start--;
		}
		while ( text.charAt( end ) && ! text.charAt( end ).match( LINE_SEPARATOR_PATTERN ) ) {
			end++;
		}
	}

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
				start,
				end
			)
		);
	} else {
		props.onChange(
			toggleFormat(
				props.value,
				{
					type: ALIGNMENT_CONTROL_NAME,
					attributes: {
						align: getAlignmentStyle( value ),
					}
				},
				start,
				end
			)
		);
	}
};

registerFormatType(
	ALIGNMENT_CONTROL_NAME,
	{
		title: __( 'Alignment Controls', 'block-lab' ),
		tagName: 'p',
		className: 'bl-aligned',
		attributes: {
			align: 'style',
		},
		edit: ( props ) => {
			const fillName = `RichText.ToolbarControls.${ ALIGNMENT_CONTROL_NAME }`;
			const value = getAlignmentFromProps( props );

			return (
				<Fill name={ fillName }>
					<AlignmentToolbar
						value={ value }
						onChange={ ( newAlignment ) => {
							onChangeAlign( newAlignment, props );
						} }
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
			<BlockLabRichText
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

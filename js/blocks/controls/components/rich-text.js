const { RichText } = wp.editor;

class BlockLabRichText extends RichText {

	constructor() {
		super( ...arguments );
		this.onSelectionChange = this.onSelectionChange.bind( this );
	}

	/**
	 * Overrides the parent implementation of the `selectionchange` handler.
	 *
	 * Mainly copies the parent implemetation.
	 * The main difference is that this allows selecting the entire format by clicking on its border.
	 */
	onSelectionChange() {
		const { start, end } = this.createRecord();
		const value = this.getRecord();
		if ( start !== value.start || end !== value.end ) {
			const { isCaretWithinFormattedText } = this.props;
			let activeFormatsCursorOnly = null;

			/*
			 * If there's only a cursor on the border of a format, not a highlighted selection,
			 * this changes the behavior from the parent RichText.
			 * Before, the RichText didn't select the format if the cursor was on a border.
			 * But this is important for alignment.
			 * So this selects the format that it's on the border of.
			 * @see https://github.com/getblocklab/block-lab/pull/221
			 */
			if ( start === end ) {
				const formatsBefore = formats[ start - 1 ] || [];
				const formatsAfter = formats[ start ] || [];
				activeFormatsCursorOnly = ( formatsBefore.length > formatsAfter.length ) ? formatsBefore : formatsAfter;
			}

			const newValue = {
				...value,
				start,
				end,
				activeFormatsCursorOnly,
			};

			const activeFormats = getActiveFormats( newValue );

			// Update the value with the new active formats.
			newValue.activeFormats = activeFormats;

			if ( ! isCaretWithinFormattedText && activeFormats.length ) {
				this.props.onEnterFormattedText();
			} else if ( isCaretWithinFormattedText && ! activeFormats.length ) {
				this.props.onExitFormattedText();
			}

			// It is important that the internal value is updated first,
			// otherwise the value will be wrong on render!
			this.record = newValue;
			this.applyRecord( newValue, { domOnly: true } );
			this.props.onSelectionChange( start, end );
			this.setState( { activeFormats } );

			if ( activeFormats.length > 0 ) {
				this.recalculateBoundaryStyle();
			}
		}
	}
}

export default BlockLabRichText
const { RichText } = wp.editor;
const { LEFT, RIGHT } = wp.keycodes;
const { getComputedStyle, isCollapsed } = wp.richText;

/**
 * Browser dependencies
 */
const { getSelection } = window;

class BlockLabRichText extends RichText {

	/**
	 * Constructs the component class.
	 */
	constructor() {
		super( ...arguments );
		this.onSelectionChange = this.onSelectionChange.bind( this );
		this.onKeyDown = this.onKeyDown.bind( this );
		this.handleHorizontalNavigation = this.handleHorizontalNavigation.bind( this );
		this.onPointerDown = this.onPointerDown.bind( this );
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

	/**
	 * Handles a keydown event.
	 *
	 * @param {SyntheticEvent} event A synthetic keyboard event.
	 */
	onKeyDown( event ) {
		const { keyCode, shiftKey, altKey, metaKey, ctrlKey } = event;
		const { onReplace, onSplit } = this.props;
		const canSplit = onReplace && onSplit;

		if (
			// Only override left and right keys without modifiers pressed.
			! shiftKey && ! altKey && ! metaKey && ! ctrlKey &&
			( keyCode === LEFT || keyCode === RIGHT )
		) {
			this.handleHorizontalNavigation( event );
		}

		// Use the space key in list items (at the start of an item) to indent
		// the list item.
		if ( keyCode === SPACE && this.multilineTag === 'li' ) {
			const value = this.createRecord();

			if ( isCollapsed( value ) ) {
				const { text, start } = value;
				const characterBefore = text[ start - 1 ];

				// The caret must be at the start of a line.
				if ( ! characterBefore || characterBefore === LINE_SEPARATOR ) {
					this.onChange( indentListItems( value, { type: this.props.tagName } ) );
					event.preventDefault();
				}
			}
		}

		if ( keyCode === DELETE || keyCode === BACKSPACE ) {
			const value = this.createRecord();
			const { replacements, text, start, end } = value;

			// Always handle full content deletion ourselves.
			if ( start === 0 && end !== 0 && end === value.text.length ) {
				this.onChange( remove( value ) );
				event.preventDefault();
				return;
			}

			if ( this.multilineTag ) {
				let newValue;

				if ( keyCode === BACKSPACE ) {
					const index = start - 1;

					if ( text[ index ] === LINE_SEPARATOR ) {
						const collapsed = isCollapsed( value );

						// If the line separator that is about te be removed
						// contains wrappers, remove the wrappers first.
						if ( collapsed && replacements[ index ] && replacements[ index ].length ) {
							const newReplacements = replacements.slice();

							newReplacements[ index ] = replacements[ index ].slice( 0, -1 );
							newValue = {
								...value,
								replacements: newReplacements,
							};
						} else {
							newValue = remove(
								value,
								// Only remove the line if the selection is
								// collapsed, otherwise remove the selection.
								collapsed ? start - 1 : start,
								end
							);
						}
					}
				} else if ( text[ end ] === LINE_SEPARATOR ) {
					const collapsed = isCollapsed( value );

					// If the line separator that is about te be removed
					// contains wrappers, remove the wrappers first.
					if ( collapsed && replacements[ end ] && replacements[ end ].length ) {
						const newReplacements = replacements.slice();

						newReplacements[ end ] = replacements[ end ].slice( 0, -1 );
						newValue = {
							...value,
							replacements: newReplacements,
						};
					} else {
						newValue = remove(
							value,
							start,
							// Only remove the line if the selection is
							// collapsed, otherwise remove the selection.
							collapsed ? end + 1 : end,
						);
					}
				}

				if ( newValue ) {
					this.onChange( newValue );
					event.preventDefault();
				}
			}

			this.onDeleteKeyDown( event );
		} else if ( keyCode === ENTER ) {
			event.preventDefault();

			const record = this.createRecord();

			if ( this.props.onReplace ) {
				const text = getTextContent( record );
				const transformation = findTransform( this.enterPatterns, ( item ) => {
					return item.regExp.test( text );
				} );

				if ( transformation ) {
					this.props.onReplace( [
						transformation.transform( { content: text } ),
					] );
					return;
				}
			}

			if ( this.multilineTag ) {
				if ( event.shiftKey ) {
					this.onChange( insert( record, '\n' ) );
				} else if ( canSplit && isEmptyLine( record ) ) {
					this.onSplit( record );
				} else {
					this.onChange( insertLineSeparator( record ) );
				}
			} else if ( event.shiftKey || ! canSplit ) {
				this.onChange( insert( record, '\n' ) );
			} else {
				this.onSplit( record );
			}
		}
	}

	/**
	 * Handles horizontal keyboard navigation when no modifiers are pressed. The
	 * navigation is handled separately to move correctly around format
	 * boundaries.
	 *
	 * @param  {SyntheticEvent} event A synthetic keyboard event.
	 */
	handleHorizontalNavigation( event ) {
		const value = this.getRecord();
		const { text, formats, start, end, activeFormats = [] } = value;
		const collapsed = isCollapsed( value );
		// To do: ideally, we should look at visual position instead.
		const { direction } = getComputedStyle( this.editableRef );
		const reverseKey = direction === 'rtl' ? RIGHT : LEFT;
		const isReverse = event.keyCode === reverseKey;

		// If the selection is collapsed and at the very start, do nothing if
		// navigating backward.
		// If the selection is collapsed and at the very end, do nothing if
		// navigating forward.
		if ( collapsed && activeFormats.length === 0 ) {
			if ( start === 0 && isReverse ) {
				return;
			}

			if ( end === text.length && ! isReverse ) {
				return;
			}
		}

		debgger
		// If the selection is not collapsed, let the browser handle collapsing
		// the selection for now. Later we could expand this logic to set
		// boundary positions if needed.
		if ( ! collapsed ) {
			return;
		}

		const formatsBefore = formats[ start - 1 ] || [];
		const formatsAfter = formats[ start ] || [];

		let newActiveFormatsLength = activeFormats.length;
		let source = formatsAfter;

		if ( formatsBefore.length > formatsAfter.length ) {
			source = formatsBefore;
		}

		// If the amount of formats before the caret and after the caret is
		// different, the caret is at a format boundary.
		if ( formatsBefore.length < formatsAfter.length ) {
			if ( ! isReverse && activeFormats.length < formatsAfter.length ) {
				newActiveFormatsLength++;
			}

			if ( isReverse && activeFormats.length > formatsBefore.length ) {
				newActiveFormatsLength--;
			}
		} else if ( formatsBefore.length > formatsAfter.length ) {
			if ( ! isReverse && activeFormats.length > formatsAfter.length ) {
				newActiveFormatsLength--;
			}

			if ( isReverse && activeFormats.length < formatsBefore.length ) {
				newActiveFormatsLength++;
			}
		}

		// Wait for boundary class to be added.
		this.props.setTimeout( () => this.recalculateBoundaryStyle() );

		if ( newActiveFormatsLength !== activeFormats.length ) {
			const newActiveFormats = source.slice( 0, newActiveFormatsLength );
			const newValue = { ...value, activeFormats: newActiveFormats };
			this.record = newValue;
			this.applyRecord( newValue );
			this.setState( { activeFormats: newActiveFormats } );
			return;
		}

		const newPos = value.start + ( isReverse ? -1 : 1 );
		const newActiveFormats = isReverse ? formatsBefore : formatsAfter;
		const newValue = {
			...value,
			start: newPos,
			end: newPos,
			activeFormats: newActiveFormats,
		};

		this.record = newValue; // @todo: maybe increment the value.start and value.end
		this.applyRecord( newValue );
		this.props.onSelectionChange( newPos, newPos );
		this.setState( { activeFormats: newActiveFormats } );
	}

	/**
	 * Select object when they are clicked. The browser will not set any
	 * selection when clicking e.g. an image.
	 *
	 * @param  {SyntheticEvent} event Synthetic mousedown or touchstart event.
	 */
	onPointerDown( event ) {
		debugger
		const { target } = event;

		// If the child element has no text content, it must be an object.
		if ( target === this.editableRef || target.textContent ) {
			return;
		}

		const { parentNode } = target;
		const index = Array.from( parentNode.childNodes ).indexOf( target );
		const range = target.ownerDocument.createRange();
		const selection = getSelection();

		range.setStart( target.parentNode, index );
		range.setEnd( target.parentNode, index + 1 );

		selection.removeAllRanges();
		selection.addRange( range );
	}
}

export default BlockLabRichText
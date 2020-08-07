/**
 * The FetchInput component, forked from the URLInput component in Gutenberg.
 *
 * @see https://github.com/WordPress/gutenberg/blob/0ede174e6ff482085ee51b6a99bea0801c11d609/packages/editor/src/components/url-input/index.js
 */

/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { __, sprintf, _n } from '@wordpress/i18n';
import { Component, createRef } from '@wordpress/element';
import { decodeEntities } from '@wordpress/html-entities';
import { UP, DOWN, ENTER } from '@wordpress/keycodes';
import { BaseControl, Spinner, withSpokenMessages, Popover } from '@wordpress/components';
import { withInstanceId } from '@wordpress/compose';
import { addQueryArgs } from '@wordpress/url';
import apiFetch from '@wordpress/api-fetch';

// Since FetchInput is rendered in the context of other inputs, but should be
// considered a separate modal node, prevent keyboard events from propagating
// as being considered from the input.
const stopEventPropagation = ( event ) => event.stopPropagation();

class FetchInput extends Component {
	/**
	 * Constructs the component class.
	 */
	constructor( { autocompleteRef } ) {
		super( ...arguments );

		this.onBlur = this.onBlur.bind( this );
		this.onFocus = this.onFocus.bind( this );
		this.onChange = this.onChange.bind( this );
		this.onKeyDown = this.onKeyDown.bind( this );
		this.autocompleteRef = autocompleteRef || createRef();
		this.inputRef = createRef();
		this.updateSuggestions = this.updateSuggestions.bind( this );
		this.setInputValidity = this.setInputValidity.bind( this );

		this.suggestionNodes = [];

		this.state = {
			loading: false,
			results: [],
			showSuggestions: false,
			selectedSuggestion: null,
		};
	}

	/**
	 * Conditionally sets the validity of the <input>.
	 *
	 * Runs when the component updates, like with a change of state.
	 *
	 * @param {Object} prevProps The previous props.
	 * @param {Object} prevState The previous state.
	 */
	componentDidUpdate( prevProps, prevState ) {
		const { results, showSuggestions } = this.state;
		const { prevResults, prevShowSuggestions } = prevState;

		// Exit if the relevant state values didn't update.
		if ( results === prevResults && showSuggestions === prevShowSuggestions ) {
			return;
		}

		if ( showSuggestions ) {
			if ( results.length ) {
				this.setInputValidity( true );
			} else {
				this.setInputValidity( false );
			}
		}
	}

	/**
	 * Deletes the request for suggestions in the Popover before this component unmounts.
	 */
	componentWillUnmount() {
		delete this.suggestionsRequest;
	}

	/**
	 * Binds the suggestion node to the ref of the button.
	 *
	 * @param {number} index The index of the suggestion.
	 * @return {Function} A function wrapping the ref.
	 */
	bindSuggestionNode( index ) {
		return ( ref ) => {
			this.suggestionNodes[ index ] = ref;
		};
	}

	/**
	 * Updates the suggested items in the Popover.
	 *
	 * @param {string} value The <input> value.
	 */
	updateSuggestions( value ) {
		this.setState( {
			loading: true,
		} );

		const request = apiFetch( {
			path: addQueryArgs( '/wp/v2/' + this.props.apiSlug, {
				search: value,
				per_page: 5,
			} ),
		} );

		request.then( ( results ) => {
			// A fetch Promise doesn't have an abort option. It's mimicked by
			// comparing the request reference in on the instance, which is
			// reset or deleted on subsequent requests or unmounting.
			if ( this.suggestionsRequest !== request ) {
				return;
			}

			this.setState( {
				results,
				showSuggestions: true,
				loading: false,
			} );

			if ( !! results.length ) {
				this.props.debouncedSpeak( sprintf( _n(
					'%d result found, use up and down arrow keys to navigate.',
					'%d results found, use up and down arrow keys to navigate.',
					results.length,
					'block-lab'
				), results.length ), 'assertive' );

				if ( null === this.state.selectedSuggestion && '' !== this.getInputValue() ) {
					this.setState( {
						selectedSuggestion: 0,
					} );
				}
			} else {
				this.props.debouncedSpeak( __( 'No results.', 'block-lab' ), 'assertive' );
			}
		} ).catch( () => {
			if ( this.suggestionsRequest === request ) {
				this.setState( {
					loading: false,
				} );
			}
		} );

		this.suggestionsRequest = request;
	}

	/**
	 * Sets the validity message and state of the <input>.
	 *
	 * On entering an invalid value, like the wrong username, this will display a message.
	 * This uses the DOM API of setCustomValidity() and reportValidity().
	 *
	 * @param {boolean} isValid Whether the value in the <input> is valid.
	 */
	setInputValidity( isValid ) {
		if ( ! this.inputRef.current || ! this.inputRef.current.setCustomValidity ) {
			return;
		}

		if ( ! isValid ) {
			this.inputRef.current.setCustomValidity( sprintf( __( 'Invalid %s', 'block-lab' ), this.props.field.control ) );
			this.inputRef.current.reportValidity();
		} else {
			this.inputRef.current.setCustomValidity( '' );
		}

		this.inputRef.current.className = classNames( 'bl-fetch__input', {
			'text-control__error': ! isValid,
		} );
	}

	/**
	 * On clicking outside the <input>, hide the Popover.
	 *
	 * Mainly taken from the color control onBlur handler.
	 * The only exception is when selecting an item by clicking a .bl-fetch-input__suggestion.
	 * That has its own handler, which will eventually hide the Popover.
	 *
	 * @param {Object} event The event.
	 */
	onBlur( event ) {
		if (
			event.relatedTarget &&
			! event.relatedTarget.classList.contains( 'components-popover__content' ) &&
			! event.relatedTarget.classList.contains( 'bl-fetch-input__suggestion' )
		) {
			this.setState( {
				showSuggestions: false,
			} );

			if ( '' === this.getInputValue() ) {
				return;
			}

			if ( false === this.inputRef.current.checkValidity() ) {
				this.handlePopoverButton( '' );
			} else {
				this.handlePopoverButton( this.state.results[ this.state.selectedSuggestion ] );
			}
		}
	}

	/**
	 * On focusing, updates the suggestions.
	 */
	onFocus() {
		this.updateSuggestions( this.getInputValue() );
	}

	/**
	 * Handles a change event by calling the component's onChange property and updating suggestions.
	 *
	 * @param {Object} event The DOM change event.
	 */
	onChange( event ) {
		const inputValue = event.target.value;
		this.props.onChange( inputValue );
		this.updateSuggestions( inputValue );
	}

	/**
	 * Handles a DOM keydown event.
	 *
	 * @param {Object} event The DOM keydown event.
	 */
	onKeyDown( event ) {
		const { showSuggestions, selectedSuggestion, results, loading } = this.state;
		const inputValue = this.getInputValue();
		// If the suggestions are not shown or loading, we shouldn't handle the arrow keys
		// We shouldn't preventDefault to allow block arrow keys navigation.
		if ( ! showSuggestions || ! results.length || loading ) {
			// In the Windows version of Firefox the up and down arrows don't move the caret
			// within an input field like they do for Mac Firefox/Chrome/Safari. This causes
			// a form of focus trapping that is disruptive to the result experience. This disruption
			// only happens if the caret is not in the first or last position in the text input.
			// See: https://github.com/WordPress/gutenberg/issues/5693#issuecomment-436684747
			switch ( event.keyCode ) {
				// When UP is pressed, if the caret is at the start of the text, move it to the 0
				// position.
				case UP: {
					if ( 0 !== event.target.selectionStart ) {
						event.stopPropagation();
						event.preventDefault();

						// Set the input caret to position 0.
						event.target.setSelectionRange( 0, 0 );
					}
					break;
				}
				// When DOWN is pressed, if the caret is not at the end of the text, move it to the
				// last position.
				case DOWN: {
					if ( inputValue.length !== event.target.selectionStart ) {
						event.stopPropagation();
						event.preventDefault();

						// Set the input caret to the last position.
						event.target.setSelectionRange( inputValue.length, inputValue.length );
					}
					break;
				}
			}

			return;
		}

		const result = this.state.results[ this.state.selectedSuggestion ];

		switch ( event.keyCode ) {
			case UP: {
				event.stopPropagation();
				event.preventDefault();
				const previousIndex = ! selectedSuggestion ? results.length - 1 : selectedSuggestion - 1;
				this.setState( {
					selectedSuggestion: previousIndex,
				} );
				break;
			}
			case DOWN: {
				event.stopPropagation();
				event.preventDefault();
				const nextIndex = selectedSuggestion === null || ( selectedSuggestion === results.length - 1 ) ? 0 : selectedSuggestion + 1;
				this.setState( {
					selectedSuggestion: nextIndex,
				} );
				break;
			}
			case ENTER: {
				if ( this.state.selectedSuggestion !== null ) {
					event.stopPropagation();
					this.handlePopoverButton( result );
					this.inputRef.current.blur();
				}
				break;
			}
		}
	}

	/**
	 * Handles actions associated with the Popover button.
	 *
	 * Including the user selecting a link in the Popover, either by clicking or using certain keys.
	 * Or the user tabbing away or blurring, which passes a '' argument and clears the <input>.
	 *
	 * @param {Object|string} result The result associated with the selected link, or '' to clear the <input>.
	 */
	handlePopoverButton( result ) {
		this.setState( {
			selectedSuggestion: null,
			showSuggestions: false,
		} );
		this.props.onChange( result );
	}

	/**
	 * Gets the value to be used in the <input>.
	 *
	 * This isn't simply this.props.value because sometimes this needs to also save an ID.
	 * For example, the Post control needs to save the post ID, and display the post title in the <input>.
	 *
	 * @return {string} The value of the <input>
	 */
	getInputValue() {
		return this.props.hasOwnProperty( 'displayValue' ) ? this.props.displayValue : this.props.value;
	}

	render() {
		const { autoFocus = false, className, getDisplayValueFromAPI, getValueFromAPI, field, instanceId } = this.props;
		const { showSuggestions, results, selectedSuggestion, loading } = this.state;
		const shouldDisplayPopover = showSuggestions && !! results.length;
		const inputValue = this.getInputValue();
		const getButtonValue = getDisplayValueFromAPI ? getDisplayValueFromAPI : getValueFromAPI;

		/* eslint-disable jsx-a11y/no-autofocus */
		return (
			<BaseControl label={ field.label } id={ `fetch-input-${ instanceId }` } className={ classNames( 'bl-fetch-input', className ) } help={ field.help }>
				<input
					autoFocus={ autoFocus }
					className="bl-fetch__input"
					type="text"
					aria-label={ field.label }
					value={ inputValue }
					onBlur={ this.onBlur }
					onFocus={ this.onFocus }
					onChange={ this.onChange }
					onInput={ stopEventPropagation }
					onKeyDown={ this.onKeyDown }
					role="combobox"
					aria-expanded={ showSuggestions }
					aria-autocomplete="list"
					aria-controls={ `bl-fetch-input-suggestions-${ instanceId }` }
					aria-owns={ `bl-fetch-input-suggestions-${ instanceId }` }
					aria-activedescendant={ selectedSuggestion !== null ? `editor-url-input-suggestion-${ instanceId }-${ selectedSuggestion }` : undefined }
					ref={ this.inputRef }
					autoComplete="off"
					autoCorrect="off"
					autoCapitalize="off"
					spellCheck="false"
				/>

				{ !! loading && <Spinner /> }

				{ shouldDisplayPopover &&
					<Popover
						position="bottom center"
						noArrow
						focusOnMount={ false }
						className={ classNames( 'bl-fetch__popover', field.location ) }
					>
						<div
							className="bl-fetch-input__suggestions"
							id={ `bl-fetch-input-suggestions-${ instanceId }` }
							ref={ this.autocompleteRef }
							role="listbox"
						>
							{ results.map( ( result, index ) => {
								const buttonValue = getButtonValue( result );

								return !! buttonValue && (
									<button
										key={ `bl-fetch-suggestion-${ index }` }
										role="option"
										tabIndex="-1"
										id={ `bl-fetch-input-suggestion-${ instanceId }-${ index }` }
										ref={ this.bindSuggestionNode( index ) }
										className={ classNames( 'bl-fetch-input__suggestion', {
											'is-selected': index === selectedSuggestion,
										} ) }
										onClick={ () => this.handlePopoverButton( result ) }
										aria-selected={ index === selectedSuggestion }
									>
										{ decodeEntities( buttonValue ) }
									</button>
								);
							} ) }
						</div>
					</Popover>
				}
			</BaseControl>
		);
		/* eslint-enable jsx-a11y/no-autofocus */
	}
}

export default withSpokenMessages( withInstanceId( FetchInput ) );

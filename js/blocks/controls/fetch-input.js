/**
 * The FetchInput component, forked from the URLInput component in Gutenberg.
 *
 * It would be ideal to extend that component instead of forking it.
 * But there are changes throughout this class.
 * For example, URLInput stores the URL and post, and this only stores the user slug.
 * Also, this FetchInput component can be reused for posts and taxonomies.
 *
 * @see https://github.com/WordPress/gutenberg/blob/0ede174e6ff482085ee51b6a99bea0801c11d609/packages/editor/src/components/url-input/index.js
 */

/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
const { __, sprintf, _n } = wp.i18n;
const { Component, createRef } = wp.element;
const { decodeEntities } = wp.htmlEntities;
const { UP, DOWN, ENTER, TAB } = wp.keycodes;
const { BaseControl, Spinner, withSpokenMessages, Popover } = wp.components;
const { withInstanceId } = wp.compose;
const apiFetch = wp.apiFetch;
const { addQueryArgs } = wp.url;

// Since FetchInput is rendered in the context of other inputs, but should be
// considered a separate modal node, prevent keyboard events from propagating
// as being considered from the input.
const stopEventPropagation = ( event ) => event.stopPropagation();

class FetchInput extends Component {
	constructor( { autocompleteRef } ) {
		super( ...arguments );

		this.onBlur = this.onBlur.bind( this );
		this.onChange = this.onChange.bind( this );
		this.onKeyDown = this.onKeyDown.bind( this );
		this.autocompleteRef = autocompleteRef || createRef();
		this.inputRef = createRef();
		this.updateSuggestions = this.updateSuggestions.bind( this );
		this.setInputValidity = this.setInputValidity.bind( this );

		this.suggestionNodes = [];

		this.state = {
			results: [],
			showSuggestions: false,
			selectedSuggestion: null,
		};
	}

	componentWillUnmount() {
		delete this.suggestionsRequest;
	}

	bindSuggestionNode( index ) {
		return ( ref ) => {
			this.suggestionNodes[ index ] = ref;
		};
	}

	updateSuggestions( value ) {
		// Show the suggestions after typing at least 2 characters
		// and also for URLs.
		if ( value.length < 2 ) {
			this.setState( {
				showSuggestions: false,
				selectedSuggestion: null,
				loading: false,
			} );

			return;
		}

		this.setState( {
			showSuggestions: true,
			selectedSuggestion: null,
			loading: true,
		} );

		const request = apiFetch( {
			path: addQueryArgs( '/wp/v2/' + this.props.apiSlug, {
				search: value,
				per_page: 20,
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
				loading: false,
			} );

			if ( !! results.length ) {
				this.props.debouncedSpeak( sprintf( _n(
					'%d result found, use up and down arrow keys to navigate.',
					'%d results found, use up and down arrow keys to navigate.',
					results.length,
					'block-lab'
				), results.length ), 'assertive' );
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
			return
		}

		if ( ! isValid ) {
			this.inputRef.current.setCustomValidity( sprintf( __( 'Invalid %s', 'block-lab' ), this.props.field.control ) );
			this.inputRef.current.reportValidity();
		} else {
			this.inputRef.current.setCustomValidity( '' );
		}
	}


	/**
	 * On clicking outside the <input>, hide the Popover.
	 *
	 * Mainly taken from the color control onBlur handler.
	 * The only exception is when selecting an item by clicking a .bl-fetch-input__suggestion.
	 * That has its own handler, which will eventually hide the Popover.
	 */
	onBlur( event ) {
		if ( event.relatedTarget && ! event.relatedTarget.classList.contains( 'bl-fetch-input__suggestion' ) ) {
			this.setState( {
				showSuggestions: false,
			} );
		}
	}

	onChange( event ) {
		const inputValue = event.target.value;
		this.props.onChange( inputValue );
		this.updateSuggestions( inputValue );
	}

	onKeyDown( event ) {
		const { showSuggestions, selectedSuggestion, results, loading } = this.state;
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
					if ( this.props.value.length !== event.target.selectionStart ) {
						event.stopPropagation();
						event.preventDefault();

						// Set the input caret to the last position.
						event.target.setSelectionRange( this.props.value.length, this.props.value.length );
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
			case TAB: {
				if ( this.state.selectedSuggestion !== null ) {
					this.selectLink( result );
					// Announce a value has been selected when tabbing away from the input field.
					this.props.speak( sprintf( __( '%s selected', 'block-lab' ), this.props.field.control ) );
				}
				break;
			}
			case ENTER: {
				if ( this.state.selectedSuggestion !== null ) {
					event.stopPropagation();
					this.selectLink( result );
				}
				break;
			}
		}
	}

	selectLink( result ) {
		this.props.onChange( result );
		this.setState( {
			selectedSuggestion: null,
			showSuggestions: false,
		} );
	}

	handleOnClick( result ) {
		this.selectLink( result );
		// Move focus to the input field when a suggestion is clicked.
		this.inputRef.current.focus();
	}

	render() {
		const { value = '', autoFocus = true, instanceId, className, placeholder, field, getValueFromAPI } = this.props;
		const { showSuggestions, results, selectedSuggestion, loading } = this.state;
		const displayPopover = showSuggestions && !! results.length;

		/* eslint-disable jsx-a11y/no-autofocus */
		return (
			<BaseControl label={ field.label } className={ classnames( 'bl-fetch-input', className ) } help={ field.help }>
				<input
					autoFocus={ autoFocus }
					className="bl-fetch__input"
					type="text"
					aria-label={ field.label }
					value={ value }
					placeholder={ placeholder }
					onBlur={ this.onBlur }
					onChange={ this.onChange }
					onInput={ stopEventPropagation }
					onKeyDown={ this.onKeyDown }
					role="combobox"
					aria-expanded={ showSuggestions }
					aria-autocomplete="list"
					aria-owns={ `bl-fetch-input-suggestions-${ instanceId }` }
					aria-activedescendant={ selectedSuggestion !== null ? `editor-url-input-suggestion-${ instanceId }-${ selectedSuggestion }` : undefined }
					ref={ this.inputRef }
				/>

				{ ( loading ) && <Spinner /> }
				{ ( displayPopover && ! loading ) && this.setInputValidity( true ) }

				{ displayPopover &&
					<Popover
						position="bottom center"
						noArrow
						focusOnMount={ false }
						className={ classnames( 'bl-fetch__popover', field.location ) }
					>
						<div
							className="bl-fetch-input__suggestions"
							id={ `bl-fetch-input-suggestions-${ instanceId }` }
							ref={ this.autocompleteRef }
							role="listbox"
						>
							{ results.map( ( result, index ) => (
								<button
									key={ result.id }
									role="option"
									tabIndex="-1"
									id={ `bl-fetch-input-suggestion-${ instanceId }-${ index }` }
									ref={ this.bindSuggestionNode( index ) }
									className={ classnames( 'bl-fetch-input__suggestion', {
										'is-selected': index === selectedSuggestion,
									} ) }
									onClick={ () => this.handleOnClick( result ) }
									aria-selected={ index === selectedSuggestion }
								>
									{ decodeEntities( getValueFromAPI( result ) ) || __( '(no result)', 'block-lab' ) }
								</button>
							) ) }
						</div>
					</Popover>
				}
				{ ! showSuggestions || ! results.length && this.setInputValidity( false ) }
			</BaseControl>
		);
		/* eslint-enable jsx-a11y/no-autofocus */
	}
}

export default withSpokenMessages( withInstanceId( FetchInput ) );

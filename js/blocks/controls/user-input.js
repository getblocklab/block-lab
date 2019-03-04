/**
 * The UserInput component, forked from the URLInput component in Gutenberg.
 *
 * It would be ideal to extend that component instead of forking it.
 * But there are changes throughout this class.
 * For example, URLInput stores the URL and post, and this only stores the user slug.
 * This still depends on styling from URLInput, including the class editor-url-input__suggestion.
 *
 * @see https://github.com/WordPress/gutenberg/blob/0ede174e6ff482085ee51b6a99bea0801c11d609/packages/editor/src/components/url-input/index.js
 */

/**
 * External dependencies
 */
import classnames from 'classnames';
import scrollIntoView from 'dom-scroll-into-view';

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

// Since UserInput is rendered in the context of other inputs, but should be
// considered a separate modal node, prevent keyboard events from propagating
// as being considered from the input.
const stopEventPropagation = ( event ) => event.stopPropagation();

class UserInput extends Component {
	constructor( { autocompleteRef } ) {
		super( ...arguments );

		this.onChange = this.onChange.bind( this );
		this.onKeyDown = this.onKeyDown.bind( this );
		this.autocompleteRef = autocompleteRef || createRef();
		this.inputRef = createRef();
		this.updateSuggestions = this.updateSuggestions.bind( this );

		this.suggestionNodes = [];

		this.state = {
			users: [],
			showSuggestions: false,
			selectedSuggestion: null,
		};
	}

	componentDidUpdate() {
		const { showSuggestions, selectedSuggestion } = this.state;
		// only have to worry about scrolling selected suggestion into view
		// when already expanded
		if ( showSuggestions && selectedSuggestion !== null && ! this.scrollingIntoView ) {
			this.scrollingIntoView = true;
			scrollIntoView( this.suggestionNodes[ selectedSuggestion ], this.autocompleteRef.current, {
				onlyScrollIfNeeded: true,
			} );

			setTimeout( () => {
				this.scrollingIntoView = false;
			}, 100 );
		}
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
		// and also for URLs
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
			path: addQueryArgs( '/wp/v2/users', {
				search: value,
				per_page: 20,
			} ),
		} );

		request.then( ( users ) => {
			// A fetch Promise doesn't have an abort option. It's mimicked by
			// comparing the request reference in on the instance, which is
			// reset or deleted on subsequent requests or unmounting.
			if ( this.suggestionsRequest !== request ) {
				return;
			}

			this.setState( {
				users,
				loading: false,
			} );

			if ( !! users.length ) {
				this.props.debouncedSpeak( sprintf( _n(
					'%d result found, use up and down arrow keys to navigate.',
					'%d results found, use up and down arrow keys to navigate.',
					users.length,
					'block-lab'
				), users.length ), 'assertive' );
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

	onChange( event ) {
		const inputValue = event.target.value;
		this.props.onChange( inputValue );
		this.updateSuggestions( inputValue );
	}

	onKeyDown( event ) {
		const { showSuggestions, selectedSuggestion, users, loading } = this.state;
		// If the suggestions are not shown or loading, we shouldn't handle the arrow keys
		// We shouldn't preventDefault to allow block arrow keys navigation
		if ( ! showSuggestions || ! users.length || loading ) {
			// In the Windows version of Firefox the up and down arrows don't move the caret
			// within an input field like they do for Mac Firefox/Chrome/Safari. This causes
			// a form of focus trapping that is disruptive to the user experience. This disruption
			// only happens if the caret is not in the first or last position in the text input.
			// See: https://github.com/WordPress/gutenberg/issues/5693#issuecomment-436684747
			switch ( event.keyCode ) {
				// When UP is pressed, if the caret is at the start of the text, move it to the 0
				// position.
				case UP: {
					if ( 0 !== event.target.selectionStart ) {
						event.stopPropagation();
						event.preventDefault();

						// Set the input caret to position 0
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

						// Set the input caret to the last position
						event.target.setSelectionRange( this.props.value.length, this.props.value.length );
					}
					break;
				}
			}

			return;
		}

		const user = this.state.users[ this.state.selectedSuggestion ];

		switch ( event.keyCode ) {
			case UP: {
				event.stopPropagation();
				event.preventDefault();
				const previousIndex = ! selectedSuggestion ? users.length - 1 : selectedSuggestion - 1;
				this.setState( {
					selectedSuggestion: previousIndex,
				} );
				break;
			}
			case DOWN: {
				event.stopPropagation();
				event.preventDefault();
				const nextIndex = selectedSuggestion === null || ( selectedSuggestion === users.length - 1 ) ? 0 : selectedSuggestion + 1;
				this.setState( {
					selectedSuggestion: nextIndex,
				} );
				break;
			}
			case TAB: {
				if ( this.state.selectedSuggestion !== null ) {
					this.selectLink( user );
					// Announce a link has been selected when tabbing away from the input field.
					this.props.speak( __( 'User selected.', 'block-lab' ) );
				}
				break;
			}
			case ENTER: {
				if ( this.state.selectedSuggestion !== null ) {
					event.stopPropagation();
					this.selectLink( user );
				}
				break;
			}
		}
	}

	selectLink( user ) {
		this.props.onChange( user.slug );
		this.setState( {
			selectedSuggestion: null,
			showSuggestions: false,
		} );
	}

	handleOnClick( user ) {
		this.selectLink( user );
		// Move focus to the input field when a link suggestion is clicked.
		this.inputRef.current.focus();
	}

	render() {
		const { value = '', autoFocus = true, instanceId, className, placeholder, field } = this.props;
		const { showSuggestions, users, selectedSuggestion, loading } = this.state;
		/* eslint-disable jsx-a11y/no-autofocus */
		return (
			<BaseControl label={ field.label } className={ classnames( 'editor-url-input', className ) } help={ field.help }>
				<input
					autoFocus={ autoFocus }
					type="text"
					aria-label={ __( 'Username', 'block-lab' ) }
					required
					value={ value }
					placeholder={ placeholder }
					onChange={ this.onChange }
					onInput={ stopEventPropagation }
					onKeyDown={ this.onKeyDown }
					role="combobox"
					aria-expanded={ showSuggestions }
					aria-autocomplete="list"
					aria-owns={ `editor-url-input-suggestions-${ instanceId }` }
					aria-activedescendant={ selectedSuggestion !== null ? `editor-url-input-suggestion-${ instanceId }-${ selectedSuggestion }` : undefined }
					ref={ this.inputRef }
				/>

				{ ( loading ) && <Spinner /> }

				{ showSuggestions && !! users.length &&
					<Popover position="bottom" noArrow focusOnMount={ false }>
						<div
							className="editor-url-input__suggestions"
							id={ `editor-url-input-suggestions-${ instanceId }` }
							ref={ this.autocompleteRef }
							role="listbox"
						>
							{ users.map( ( user, index ) => (
								<button
									key={ user.id }
									role="option"
									tabIndex="-1"
									id={ `editor-url-input-suggestion-${ instanceId }-${ index }` }
									ref={ this.bindSuggestionNode( index ) }
									className={ classnames( 'editor-url-input__suggestion', {
										'is-selected': index === selectedSuggestion,
									} ) }
									onClick={ () => this.handleOnClick( user ) }
									aria-selected={ index === selectedSuggestion }
								>
									{ decodeEntities( user.slug ) || __( '(no username)', 'block-lab' ) }
								</button>
							) ) }
						</div>
					</Popover>
				}
			</BaseControl>
		);
		/* eslint-enable jsx-a11y/no-autofocus */
	}
}

export default withSpokenMessages( withInstanceId( UserInput ) );

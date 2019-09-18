/**
 * WordPress dependencies
 */
const { Component } = wp.element;
const { __ } = wp.i18n;
const { BACKSPACE, DELETE, F10 } = wp.keycodes;

function isTmceEmpty( editor ) {
	// When tinyMce is empty the content seems to be:
	// <p><br data-mce-bogus="1"></p>
	// avoid expensive checks for large documents
	const body = editor.getBody();
	if ( body.childNodes.length > 1 ) {
		return false;
	} else if ( body.childNodes.length === 0 ) {
		return true;
	}
	if ( body.childNodes[ 0 ].childNodes.length > 1 ) {
		return false;
	}
	return /^\n?$/.test( body.innerText || body.textContent );
}

/**
 * Forked from the Core Classic Editor's Edit component.
 *
 * @see https://github.com/WordPress/gutenberg/blob/416c9bc9eaef6e6bceea923b6f36642746c01aba/packages/block-library/src/classic/edit.js
 */
export default class TinyMCE extends Component {
	constructor( props ) {
		super( props );
		this.initialize = this.initialize.bind( this );
		this.onSetup = this.onSetup.bind( this );
		this.focus = this.focus.bind( this );
	}

	componentDidMount() {
		const { baseURL, suffix } = window.wpEditorL10n.tinymce;

		window.tinymce.EditorManager.overrideDefaults( {
			base_url: baseURL,
			suffix,
		} );

		if ( document.readyState === 'complete' ) {
			this.initialize();
		} else {
			window.addEventListener( 'DOMContentLoaded', this.initialize );
		}
	}

	componentWillUnmount() {
		window.addEventListener( 'DOMContentLoaded', this.initialize );
		wp.oldEditor.remove( `editor-${ this.props.clientId }` );
	}

	componentDidUpdate( prevProps ) {
		const { clientId, content } = this.props;

		const editor = window.tinymce.get( `editor-${ clientId }` );

		if ( prevProps.content !== content ) {
			editor.setContent( content || '' );
		}
	}

	initialize() {
		const { clientId } = this.props;
		const { settings } = window.wpEditorL10n.tinymce;
		wp.oldEditor.initialize( `editor-${ clientId }`, {
			tinymce: {
				...settings,
				inline: true,
				content_css: false,
				fixed_toolbar_container: `#toolbar-${ clientId }`,
				setup: this.onSetup,
				toolbar1: 'bold,italic,bullist,numlist,outdent,indent,alignleft,aligncenter,alignright,link,unlink,wp_add_media',
				toolbar2: '',
			},
		} );
	}

	onSetup( editor ) {
		const { content, onChange } = this.props;
		const { ref } = this;
		let bookmark;

		this.editor = editor;

		if ( content ) {
			editor.on( 'loadContent', () => editor.setContent( content ) );
		}

		editor.on( 'blur', () => {
			bookmark = editor.selection.getBookmark( 2, true );

			onChange( editor.getContent() );

			editor.once( 'focus', () => {
				if ( bookmark ) {
					editor.selection.moveToBookmark( bookmark );
				}
			} );

			return false;
		} );

		editor.on( 'mousedown touchstart', () => {
			bookmark = null;
		} );

		editor.on( 'keydown', ( event ) => {
			if ( ( event.keyCode === BACKSPACE || event.keyCode === DELETE ) && isTmceEmpty( editor ) ) {
				// delete the block
				this.props.onReplace( [] );
				event.preventDefault();
				event.stopImmediatePropagation();
			}

			const { altKey } = event;
			/*
			 * Prevent Mousetrap from kicking in: TinyMCE already uses its own
			 * `alt+f10` shortcut to focus its toolbar.
			 */
			if ( altKey && event.keyCode === F10 ) {
				event.stopPropagation();
			}
		} );

		// Show the second, third, etc. toolbars when the `kitchensink` button is removed by a plugin.
		editor.on( 'init', function() {
			if ( editor.settings.toolbar1 && editor.settings.toolbar1.indexOf( 'kitchensink' ) === -1 ) {
				editor.dom.addClass( ref, 'has-advanced-toolbar' );
			}
		} );

		editor.addButton( 'wp_add_media', {
			tooltip: __( 'Insert Media' ),
			icon: 'dashicon dashicons-admin-media',
			cmd: 'WP_Medialib',
		} );

		editor.on( 'init', () => {
			const rootNode = this.editor.getBody();

			// Create the toolbar by refocussing the editor.
			if ( document.activeElement === rootNode ) {
				rootNode.blur();
				this.editor.focus();
			}
		} );
	}

	focus() {
		if ( this.editor ) {
			this.editor.focus();
		}
	}

	onToolbarKeyDown( event ) {
		// Prevent WritingFlow from kicking in and allow arrows navigation on the toolbar.
		event.stopPropagation();
		// Prevent Mousetrap from moving focus to the top toolbar when pressing `alt+f10` on this block toolbar.
		event.nativeEvent.stopImmediatePropagation();
	}

	render() {
		const { clientId } = this.props;

		// Disable reasons:
		//
		// jsx-a11y/no-static-element-interactions
		//  - the toolbar itself is non-interactive, but must capture events
		//    from the KeyboardShortcuts component to stop their propagation.

		/* eslint-disable jsx-a11y/no-static-element-interactions */
		return [
			<div
				key="toolbar"
				id={ `toolbar-${ clientId }` }
				ref={ ( ref ) => this.ref = ref }
				className="rich-text__toolbar"
				onClick={ this.focus }
				data-placeholder={ __( 'Classic', 'block-lab' ) }
				onKeyDown={ this.onToolbarKeyDown }
			/>,
			<div
				key="editor"
				id={ `editor-${ clientId }` }
				className="rich-text__edit block-library-rich-text__tinymce"
			/>,
		];
		/* eslint-enable jsx-a11y/no-static-element-interactions */
	}
}

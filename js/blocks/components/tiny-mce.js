/**
 * WordPress dependencies
 */
import { Component } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { F10 } from '@wordpress/keycodes';

/**
 * Forked from the Core Classic Editor's Edit component.
 *
 * @see https://github.com/WordPress/gutenberg/blob/416c9bc9eaef6e6bceea923b6f36642746c01aba/packages/block-library/src/classic/edit.js
 */
class TinyMCE extends Component {
	/**
	 * Constructs the class.
	 *
	 * @param {Object} props The component properties.
	 */
	constructor( props ) {
		super( props );
		this.initialize = this.initialize.bind( this );
		this.onSetup = this.onSetup.bind( this );
		this.focus = this.focus.bind( this );
	}

	/**
	 * A lifecycle method, called after the component mounts.
	 */
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

	/**
	 * A lifecycle method, called before the component unmounts.
	 */
	componentWillUnmount() {
		window.addEventListener( 'DOMContentLoaded', this.initialize );
		wp.oldEditor.remove( `editor-${ this.props.editorId }` );
	}

	/**
	 * A lifecycle method, called after the component updates.
	 *
	 * @param {Object} prevProps The previous props of the component, before the update.
	 */
	componentDidUpdate( prevProps ) {
		const { content, onChange } = this.props;

		if ( prevProps.content !== content ) {
			onChange( content || '' );
		}
	}

	/**
	 * Initializes the TinyMCE.
	 */
	initialize() {
		const { editorId } = this.props;
		const { settings } = window.wpEditorL10n.tinymce;
		wp.oldEditor.initialize( `editor-${ editorId }`, {
			tinymce: {
				...settings,
				inline: true,
				content_css: false,
				fixed_toolbar_container: `#toolbar-${ editorId }`,
				setup: this.onSetup,
				toolbar1: 'formatselect,bold,italic,bullist,numlist,outdent,indent,alignleft,aligncenter,alignright,link,unlink,wp_add_media,strikethrough',
				toolbar2: '',
			},
		} );
	}

	/**
	 * Handles events in the editor.
	 *
	 * @param {Object} editor The editor.
	 */
	onSetup( editor ) {
		const { content, onChange } = this.props;
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

		// Different from the Core Classic block, prevents losing edits when viewing the <ServerSideRender>.
		editor.on( 'change', () => {
			onChange( editor.getContent() );
		} );

		editor.on( 'keydown', ( event ) => {
			const { altKey } = event;
			/*
			 * Prevent Mousetrap from kicking in: TinyMCE already uses its own
			 * `alt+f10` shortcut to focus its toolbar.
			 */
			if ( altKey && event.keyCode === F10 ) {
				event.stopPropagation();
			}
		} );

		editor.addButton( 'wp_add_media', {
			tooltip: __( 'Insert Media', 'block-lab' ),
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

	/**
	 * If the editor exists, this focuses it.
	 */
	focus() {
		if ( this.editor ) {
			this.editor.focus();
		}
	}

	/**
	 * Handles a keydown event for the toolbar.
	 *
	 * @param {Object} event The keydown event.
	 */
	onToolbarKeyDown( event ) {
		// Prevent WritingFlow from kicking in and allow arrows navigation on the toolbar.
		event.stopPropagation();
		// Prevent Mousetrap from moving focus to the top toolbar when pressing `alt+f10` on this block toolbar.
		event.nativeEvent.stopImmediatePropagation();
	}

	/**
	 * Renders the component.
	 */
	render() {
		const { editorId } = this.props;

		// Disable reasons:
		//
		// jsx-a11y/no-static-element-interactions
		//  - the toolbar itself is non-interactive, but must capture events
		//    from the KeyboardShortcuts component to stop their propagation.

		/* eslint-disable jsx-a11y/no-static-element-interactions */
		return [
			<div
				key="toolbar"
				id={ `toolbar-${ editorId }` }
				className="classic-text__toolbar"
				onClick={ this.focus }
				data-placeholder={ __( 'Classic text', 'block-lab' ) }
				onKeyDown={ this.onToolbarKeyDown }
			/>,
			<div
				key="editor"
				id={ `editor-${ editorId }` }
				className="classic-text__edit"
			/>,
		];
		/* eslint-enable jsx-a11y/no-static-element-interactions */
	}
}

export default TinyMCE;

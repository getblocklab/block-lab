/**
 * WordPress dependencies
 */
const { blocks } = wp;
const { TextControl } = wp.components;
const { compose } = wp.compose;
const { withDispatch, withSelect } = wp.data;
const { Component } = wp.element;
const { __ } = wp.i18n;

/**
 * The Block Lab field editor.
 */
class BlockLabEditor extends Component {
	/**
	 * Renders the editor.
	 *
	 * @return {Function} The rendered component.
	 */
	render() {
		const { content, onChange } = this.props;

		return (
			<TextControl
				label={ __( 'Proof of concept that it is possible to directly change post content from a meta box', 'block-lab' ) }
				value={ content }
				onChange={ onChange }
			/>
		);
	}
}

export default compose( [
	withSelect( ( select ) => {
		return {
			content: select( 'core/editor' ).getEditedPostContent(),
		};
	} ),
	withDispatch( ( dispatch ) => {
		const store = dispatch( 'core/editor' );

		return {
			onChange( content ) {
				store.editPost( { content } );
				store.resetEditorBlocks( blocks.parse( content ) );
			},
		};
	} ),
] )( BlockLabEditor );

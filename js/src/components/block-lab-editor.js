/**
 * WordPress dependencies
 */
const { blocks } = wp;
const { Button } = wp.components;
const { compose } = wp.compose;
const { withDispatch, withSelect } = wp.data;
const { Component } = wp.element;
const { __, sprintf } = wp.i18n;

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
		return (
			<div>
				<div className="block-fields-list">
					<table className="widefat">
						<thead>
							<tr>
								<th className="block-fields-sort"></th>
								<th className="block-fields-label">
									{ __( 'Field Label', 'block-lab' ) }
								</th>
								<th className="block-fields-name">
									{ __( 'Field Name', 'block-lab' ) }
								</th>
								<th className="block-fields-control">
									{ __( 'Field Type', 'block-lab' ) }
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td colSpan="4">
									<div className="block-fields-rows">
										<p className="block-no-fields">
											{ sprintf( __( 'Click %sAdd Field%s below to add your first field.', 'block-lab' ), '<strong>', '</strong' ) }
										</p>
										{ /* @todo: implement render_fields_meta_box_row() here. */ }
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div className="block-fields-actions-add-field">
					<Button type="button" aria-label="Add Field" className="block-fields-action" id="block-add-field">
						<span className="dashicons dashicons-plus"></span>
						{ __( 'Add Field', 'block-lab' ) }
					</Button>
					{ /* @todo: reimplement render_fields_meta_box_row and render_fields_sub_rows() */ }
				</div>
			</div>
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

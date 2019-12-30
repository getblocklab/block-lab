/**
 * WordPress dependencies
 */
const { blocks } = wp;
const { Button } = wp.components;
const { compose } = wp.compose;
const { withDispatch, withSelect } = wp.data;
const { Component } = wp.element;
const { __ } = wp.i18n;

/**
 * Internal dependencies
 */
import getBlockFromContent from '../helpers/getBlockFromContent';
import { Field } from '.';

/**
 * The Block Lab field editor.
 */
class Editor extends Component {
	/**
	 * Renders the editor.
	 *
	 * @return {Function} The rendered component.
	 */
	render() {
		const { content } = this.props;
		const parsedBlock = getBlockFromContent( content );
		const { fields } = parsedBlock;

		return (
			<div className="block-builder">
				<div className="header">
					<div className="header-nav">
						<Button className="header-nav-item header-nav-item--active" href="#">{ __( 'Builder', 'block-lab' ) }</Button>
						<Button className="header-nav-item" href="#">{ __( 'Editor Preview', 'block-lab' ) }</Button>
					</div>
				</div>
				<div className="main">
					<div className="fields">
						{
							!! fields && Object.values( fields ).map( ( field, index ) => {
								return <Field field={ field } uiud={ index } key={ `field-row-${ index }` } />;
							} )
						}
						<div className="add-field-container">
							<button>
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
									<path fill="none" d="M0 0h24v24H0V0z" />
									<path d="M13 7h-2v4H7v2h4v4h2v-4h4v-2h-4V7zm-1-5C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z" />
								</svg>
								Add Field
							</button>
						</div>
					</div>
				</div>
				<div className="side"></div>
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
] )( Editor );

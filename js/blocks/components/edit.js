/**
 * WordPress dependencies
 */
const { ServerSideRender } = wp.editor;
const { Fragment } = wp.element;

/**
 * Internal dependencies
 */
import { AdvancedControls, BlockLabInspector, FormControls } from './';
import icons from '../../../assets/icons.json';

/**
 * The Edit function for the block.
 *
 * @param {Object} blockProps The block's props.
 * @param {Object} block The block.
 * @return {Function} The Edit function for the block.
 */
const Edit = ( { blockProps, block } ) => {
	const { attributes, className, isSelected } = blockProps;

	if ( 'undefined' === typeof icons[ block.icon ] ) {
		icons[ block.icon ] = '';
	}

	return (
		<Fragment>
			<BlockLabInspector blockProps={ blockProps } block={ block } />
			<AdvancedControls block={ block } />
			<div className={ className } key={ `form-controls-${ block.name }` } >
				{ isSelected ? (
					<div className="block-form">
						<h3 dangerouslySetInnerHTML={ { __html: icons[ block.icon ] + ' ' + block.title } } />
						<FormControls blockProps={ blockProps } block={ block } />
					</div>
				) : (
					<ServerSideRender
						block={ `block-lab/${ block.name }` }
						attributes={ attributes }
					/>
				) }
			</div>
		</Fragment>
	);
};

export default Edit;

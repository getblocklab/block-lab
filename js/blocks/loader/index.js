/**
 * External dependencies
 */
const { blockLab, blockLabBlocks } = window;

/**
 * WordPress dependencies
 */
const { registerBlockType } = wp.blocks;

/**
 * Internal dependencies
 */
import icons from '../../../assets/icons.json';
import getBlockAttributes from './attributes';
import { Edit } from '../components';
import '../../../css/src/editor.scss';

const registerBlocks = () => {
	// Loop through all the blocks.
	// Note: This is not guaranteed to be sequential.
	for ( const blockName in blockLabBlocks ) {
		// Avoid weird inheritance issues. Which should not happen because the backend is safe.
		if ( ! blockLabBlocks.hasOwnProperty( blockName ) ) {
			continue;
		}

		// Get the block definition.
		const block = blockLabBlocks[ blockName ];
		block.block_slug = blockName;

		// Don't register the block if it's excluded for this post type.
		if ( blockLab.hasOwnProperty( 'postType' ) && block.hasOwnProperty( 'excluded' ) ) {
			if ( -1 !== block.excluded.indexOf( blockLab.postType ) ) {
				continue;
			}
		}

		let icon = '';
		if ( 'undefined' !== typeof icons[ block.icon ] ) {
			icon = (
				<span dangerouslySetInnerHTML={ { __html: icons[ block.icon ] } } />
			);
		}

		// Register the block.
		registerBlockType( blockName, {
			title: block.title,
			category: 'object' === typeof block.category ? block.category.slug : block.category,
			icon,
			keywords: block.keywords,
			attributes: getBlockAttributes( block.fields ),
			edit: ( props ) => {
				return <Edit blockProps={ props } block={ block } />;
			},
			save() {
				return null;
			},
		} );
	}
};

export default registerBlocks();

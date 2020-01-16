/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import icons from '../../../assets/icons.json';
import { getBlockLabAttributes } from './';

/**
 * Loops through all of the blocks, but not guaranteed to be sequential.
 *
 * @param {Object} blockLab Block Lab properties, available via wp_localize_script().
 * @param {Object} blockLabBlocks The registered Block Lab blocks, available via wp_add_inline_script().
 * @param {Function} EditComponent The edit component to render the blocks.
 */
const registerBlocks = ( blockLab, blockLabBlocks, EditComponent ) => {
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
			attributes: getBlockLabAttributes( block.fields ),
			edit( props ) {
				return <EditComponent blockProps={ props } block={ block } />;
			},
			save() {
				return null;
			},
		} );
	}
};

export default registerBlocks;

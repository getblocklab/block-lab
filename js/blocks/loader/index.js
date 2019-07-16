import icons from '../../../assets/icons.json';

import blockAttributes from './attributes'
import editComponent from './edit'

import './editor.scss';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;

const registerBlocks = () => {

	// Loop through all the blocks.
	// Note: This is not guaranteed to be sequential.
	for ( let blockName in blockLabBlocks ) {

		// Avoid weird inheritance issues. Which should not happen because the backend is safe.
		if ( !blockLabBlocks.hasOwnProperty( blockName ) ) continue;

		// Get the block definition.
		let block = blockLabBlocks[ blockName ];
		block.block_slug = blockName;

		let icon = '';
		if ( 'undefined' !== typeof icons[ block.icon ] ) {
			icon = (
				<span dangerouslySetInnerHTML={{ __html: icons[ block.icon ] }} />
			);
		}

		// Register the block.
		registerBlockType( blockName, {
			title: block.title,
			category: 'object' === typeof block.category ? block.category.slug : block.category,
			icon: icon,
			keywords: block.keywords,
			attributes: blockAttributes( block ),
			edit: props => {
				return editComponent(props, block)
			},
			save() {
				return null
			},
		} )
	}
}

export default registerBlocks()
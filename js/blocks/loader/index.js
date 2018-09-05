import icons from '../icons'

import blockAttributes from './attributes'
import editComponent from './edit'

import './editor.scss';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;

const registerAdvancedCustomBlocks = () => {

	// Loop through all the blocks.
	// Note: This is not guaranteed to be sequential.
	for ( let blockName in acbBlocks ) {

		// Avoid weird inheritance issues. Which should not happen because the backend is safe.
		if ( !acbBlocks.hasOwnProperty( blockName ) ) continue;

		// Get the block definition.
		let block = acbBlocks[ blockName ];
		block.block_slug = blockName;

		let icon = icons.logo;
		if ( 'undefined' !== typeof block.icon && '' !== block.icon ) {
			icon = <i className="material-icons">{block.icon}</i>;
		}

		// Register the block.
		registerBlockType( blockName, {
			title: block.title,
			description: block.description,
			category: block.category,
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

export default registerAdvancedCustomBlocks()
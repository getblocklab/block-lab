import icons from '../icons'

import blockAttributes from './attributes'
import editComponent from './edit'

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

		// Register the block.
		registerBlockType( blockName, {
			title: block.title,
			description: block.description,
			category: block.category,
			icon: () => {
				if ( '' === block.icon || 'default' === block.icon ) {
					return icons.logo
				}
				return block.icon
			},
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
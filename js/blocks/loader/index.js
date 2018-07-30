import icons from '../icons'

import blockAttributes from './attributes'
import editComponent from './edit'
import saveComponent from './save'

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;

// @todo Replace these mock blocks with dynamic data fed by PHP.
const acbBlocks = {
	'advanced-custom-blocks/block-one': {
		title: __( 'ACB: Block1', 'advanced-custom-blocks' ),
		description: __( 'This should come from the PHP backend.', 'advanced-custom-blocks' ),
		category: 'common',
		icon: icons.logo,
		keywords: [
			__( 'ACB Block1', 'advanced-custom-blocks' ),
		],
		groups: {},
		fields: {
			postID: {},
			fieldOne: {
				label: 'Field One',
				type: 'string',
				source: 'meta',
				meta: 'field-one-meta-key',
				control: 'text',
				icon: 'admin-generic',
				location: [
					'inspector',
				],
				order: 1
			},
			fieldTwo: {
				label: 'Field Two',
				type: 'string',
				source: 'meta',
				meta: 'field-two-meta-key',
				control: 'textarea',
				icon: 'admin-generic',
				location: [
					'inspector',
				],
				order: 2
			},
			fieldThree: {
				label: 'Field Three',
				type: 'string',
				source: 'meta',
				meta: 'field-three-meta-key',
				control: 'radio',
				options: {
					'one': 'Option One',
					'two': 'Option Two',
				},
				default: 'one',
				icon: 'admin-generic',
				location: [
					'inspector',
					'editor'
				],
				order: 3
			},
			fieldFour: {
				label: 'Field Four',
				type: 'string',
				source: 'meta',
				meta: 'field-four-meta-key',
				control: 'checkbox',
				options: {
					'one': 'Option One',
					'two': 'Option Two',
				},
				default: [ 'two' ],
				icon: 'admin-generic',
				location: [
					'inspector',
					'editor'
				],
				order: 4
			},
			fieldFive: {
				label: 'Field Five',
				type: 'string',
				meta: 'field-five-meta-key',
				control: 'toggle',
				default: 'off',
				icon: 'admin-generic',
				iconOn: 'admin-generic',
				iconOff: 'admin-generic',
				location: [
					'toolbar',
				],
				order: 5
			},
		}
	}
}

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
			icon: block.icon,
			keywords: block.keywords,
			attributes: blockAttributes( block ),
			edit: editComponent,
			save: saveComponent,
		} )
	}
}

export default registerAdvancedCustomBlocks()
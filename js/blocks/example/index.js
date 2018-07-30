import icons from '../icons'
import './editor.scss'


const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { Button } = wp.components;

export default registerBlockType(
	'advanced-custom-blocks/example',
	{
		title: __( 'ACB: Example', 'advanced-custom-blocks' ),
		description: __( 'Example block.', 'advanced-custom-blocks' ),
		category: 'common',
		icon: icons.logo,
		keywords: [
			__( 'Example Block', 'advanced-custom-blocks' ),
		],
		attributes: {},
		edit: props => {
			const { className, isSelected } = props;

			return (
				<div className={className}>
					{ __( 'Example block content [EDIT]', 'advanced-custom-blocks' ) }
					{ isSelected ? (
						<p>{ __( '[SELECTED]', 'advanced-custom-blocks' ) }</p>
					) : null}
				</div>
			);
		},
		save: props => {
			return (
				<p>{ __( 'Example block content [SAVE]', 'advanced-custom-blocks' ) }</p>
			);
		},
	},
);
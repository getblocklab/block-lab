import inspectorControls from './inspector'

const { __ } = wp.i18n;

const editComponent = (props, block) => {
	const { className, isSelected } = props;

	return [
		inspectorControls(props, block),
		(
			<div className={className}>
				{__( 'Example block content [EDIT]', 'advanced-custom-blocks' )}
				{isSelected ? (
					<p>{__( '[SELECTED]', 'advanced-custom-blocks' )}</p>
				) : null}
			</div>
		)
	]
}

export default editComponent
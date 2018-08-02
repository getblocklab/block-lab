import inspectorControls from './inspector'
import { getControl } from "./controls";
import { simplifiedFields } from "./fields";
import updatePreview from './preview';
import icons from '../icons'

const { __ } = wp.i18n;
const { RichText } = wp.editor;

const formControls = ( props, block ) => {

	const fields = simplifiedFields( block.fields ).map( field => {

		// If its not meant for the inspector then continue (return null).
		// if ( !field.location ) {
		if ( !field.location || !field.location.includes( 'editor' ) ) {
			return null
		}

		return (
			<div>
				{getControl( props, field, block )}
			</div>
		)
	} )

	return (
		<div>
			{fields}
		</div>
	)
}

const previewData = ( props, block ) => {
	if ( typeof props.attributes.block_template !== 'undefined' && props.attributes.block_template.length > 0 ) {
		return;
	}

	wp.apiFetch( { path: `/acb/v1/block-preview?slug=` + block.name } ).then(
		data => {
			props.setAttributes( { block_template: data } );
			updatePreview( props, block, data );
		}
	);
};


const editComponent = ( props, block ) => {
	const { className, isSelected } = props;

	previewData( props, block )

	return [
		inspectorControls( props, block ),
		(
			<div className={className}>
				{isSelected ? (
					<div className="block-form">
						<h3>{icons.logo} {block.title}</h3>
						<div>
							{formControls( props, block )}
						</div>
					</div>
				) : (
					<RichText
						value={props.attributes.block_preview || __( 'Loading preview...', 'advanced-custom-blocks' )}
						onChange={e => {
							e.preventDefault;
						}}
						format="string"
					/>
				)}
			</div>
		),
	]
}

export default editComponent
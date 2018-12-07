import inspectorControls from './inspector'
import controls from '../controls';
import { simplifiedFields } from "./fields";
import icons from '../../../assets/icons.json';

const { __ } = wp.i18n;
const { ServerSideRender } = wp.editor;

const formControls = ( props, block ) => {

	const fields = simplifiedFields( block.fields ).map( (field, index) => {

		// If its not meant for the editor then continue (return null).
		if ( !field.location || !field.location.includes( 'editor' ) ) {
			return null
		}

		const controlFunction = field.controlFunction || controls[ field.control ];
		const control = typeof controlFunction !== 'undefined' ? controlFunction( props, field, block ) : null;

		return (
			<div key={field.name + "-" + index}>
				{control}
			</div>
		)
	} )

	return (
		<div key={ block.name + "-fields" }>
			{fields}
		</div>
	)
}

const editComponent = ( props, block ) => {
	const { className, isSelected } = props;

	if ( 'undefined' === typeof icons[block.icon] ) {
		icons[block.icon] = ''
	}

	return [
		inspectorControls( props, block ),
		(
			<div className={className} key={"form-controls-" + block.name}>
				{isSelected ? (
					<div className="block-form">
						<h3 dangerouslySetInnerHTML={{ __html: icons[block.icon] + ' ' + block.title }} />
						<div>
							{formControls( props, block )}
						</div>
					</div>
				) : (
					<ServerSideRender
						block={'block-lab/' + block.name}
						attributes={props.attributes}
					/>
				)}
			</div>
		),
	]
}

export default editComponent
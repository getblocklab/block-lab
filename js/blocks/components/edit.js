/**
 * Internal dependencies
 */
import inspectorControls from '../loader/inspector'
import { AdvancedControls, Fields } from './';
import icons from '../../../assets/icons.json';

/**
 * WordPress dependencies
 */
const { ServerSideRender } = wp.editor;

const FormControls = ( props, block ) => {
	return (
		<div key={ block.name + "-fields" }>
			<Fields
				fields={ block.fields }
				parentBlockProps={ props }
				parentBlock={ block }
			/>
		</div>
	)
};

export default ( props, block ) => {
	const { className, isSelected } = props;

	if ( 'undefined' === typeof icons[block.icon] ) {
		icons[block.icon] = ''
	}

	return [
		inspectorControls( props, block ),
		AdvancedControls( props, block ),
		(
			<div className={className} key={"form-controls-" + block.name}>
				{isSelected ? (
					<div className="block-form">
						<h3 dangerouslySetInnerHTML={{ __html: icons[block.icon] + ' ' + block.title }} />
						<div>
							{ FormControls( props, block ) }
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
};

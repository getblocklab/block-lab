import inspectorControls from './inspector'
import inspectorAdvancedControls from './advanced'
import controls from '../controls';
import { simplifiedFields } from "./fields";
import icons from '../../../assets/icons.json';

const { applyFilters } = wp.hooks;
const { ServerSideRender } = wp.editor;

const formControls = ( props, block ) => {
	const fields = simplifiedFields( block.fields ).map( ( field, index ) => getRenderedControl( block, props, field, index ) );

	return (
		<div key={ block.name + "-fields" }>
			{ fields }
		</div>
	)
}

/**
 * Gets the rendered control, based on the field values.
 *
 * @param {Object} block The block that has the control.
 * @param {Object} props The block props.
 * @param {Object} field The field to render.
 * @param {number} index The index in the block.
 * @return {Function|null} The rendered control as JSX, or null.
 */
const getRenderedControl = ( block, props, field, index ) => {
	if ( field.location && ! field.location.includes( 'editor' ) ) {
		return null // This is not meant for the editor.
	}

	const controlFunction = getControlFunction( field );
	const control = controlFunction ? controlFunction( props, field, block ) : null;
	const renderedSubFields = getRenderedSubFields( block, props, field );

	return (
		<div key={ field.name + "-" + index }>
			{ control }
			{ renderedSubFields }
		</div>
	)
};

/**
 * Gets the rendered control, based on the field values.
 *
 * @param {Object} block The block that has the control.
 * @param {Object} props The block props.
 * @param {Object} field The field to render.
 * @return {array|null} The rendered sub-fields.
 */
const getRenderedSubFields = ( block, props, field ) => {
	const renderedSubFields = [];
	if ( field[ 'sub-fields' ] ) {
		for ( const subFieldName in field[ 'sub-fields' ] ) {
			if ( field[ 'sub-fields' ].hasOwnProperty( subFieldName ) ) {
				const subField = field[ 'sub-fields' ][ subFieldName ];
				const controlFunction = getControlFunction( subField );
				if ( controlFunction ) {
					renderedSubFields.push( controlFunction( props, subField, block ) );
				}
			}
		}
	} else {
		return null;
	}

	return (
		<div class="block-form">
			{ renderedSubFields }
		</div>
	);
};

/**
 * Gets the control function for the field.
 *
 * @param {Object} field The field to get the control function of.
 * @return {Function} The control function.
 */
const getControlFunction = ( field ) => {
	if ( field.hasOwnProperty( 'controlFunction' ) ) {
		return field.controlFunction;
	}

	const loadedControls = applyFilters( 'block_lab_controls', controls );
	return loadedControls[ field.control ];
};

const editComponent = ( props, block ) => {
	const { className, isSelected } = props;

	if ( 'undefined' === typeof icons[block.icon] ) {
		icons[block.icon] = ''
	}

	return [
		inspectorControls( props, block ),
		inspectorAdvancedControls( props, block ),
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
};

export default editComponent
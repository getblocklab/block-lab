import inspectorControls from './inspector'
import inspectorAdvancedControls from './advanced'
import controls from '../controls';
import { simplifiedFields } from "./fields";
import icons from '../../../assets/icons.json';

const { __ } = wp.i18n;
const { BaseControl, IconButton } = wp.components;
const { ServerSideRender } = wp.editor;
const { Component } = wp.element;
const { applyFilters } = wp.hooks;

const formControls = ( props, block ) => {
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

/**
 * Gets the rendered control, based on the field values.
 *
 * @param {Object}        parentBlock      The block that has the control.
 * @param {Object}        parentBlockProps The block props.
 * @param {Object}        field            The field to render.
 * @param {number|string} index            The index in the block, or the row name if one exists.
 * @return {Function|null} The rendered control as JSX, or null.
 */
const RenderedControl = ( { parentBlock, parentBlockProps, field } ) => {
	if ( field.location && ! field.location.includes( 'editor' ) ) {
		return null; // This is not meant for the editor.
	}

	const controlFunction = getControlFunction( field );
	const control = controlFunction ? controlFunction( parentBlockProps, field, parentBlock ) : null;

	return (
		<div key={ `${ field.name }-control` }>
			{ control }
		</div>
	)
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

/**
 * Renders the fields, using their control functions.
 *
 * @param {Array}  fields           The fields to render.
 * @param {Object} parentBlockProps The props to pass to the control function.
 * @param {Object} parentBlock      The block where the fields are.
 * @param {String} rowName          The name of the repeater row, if this field is in one (optional).
 * @return {Function} fields The rendered fields.
 */
export const Fields = ( { fields, parentBlockProps, parentBlock, rowName = null } ) => {
	return simplifiedFields( fields, rowName ).map( ( field, index ) => {
		return (
			<RenderedControl
				parentBlock={ parentBlock }
				parentBlockProps={ parentBlockProps }
				field={ field }
				index={ index }
				key={ field.name }
			/>
		);
	} );
};

/**
 * Gets the rendered fields, using their control functions.
 *
 * @param {Array}  rows   The repeater rows to render.
 * @param {Array}  fields The fields to render.
 * @param {Object} parentBlockProps  The props to pass to the control function.
 * @param {Object} parentBlock  The block where the fields are.
 * @return {Array} fields The rendered fields.
 */
 export class RepeaterRows extends Component {

	/**
	 * Constructs the component class.
	 */
	constructor() {
		super( ...arguments );
		this.removeRow = this.removeRow.bind( this );

		this.state = {
			activeFieldset: 0,
		};
	}

	/*
	 * On clicking the 'remove' button in a repeater row, this removes it.
	 *
	 * @param {Number} index The index of the row to remove, 0 being the first.
	 */
	removeRow( index ) {
		return () => {
			const { parentBlockProps } = this.props;
			const attr = { ...parentBlockProps.attributes };
			const parentName = getParent( fields );
			const repeaterRows = attr[ parentName ];
			if ( ! repeaterRows ) {
				return;
			}

			/*
			 * Calling slice() essentially creates a copy of repeaterRows.
			 * Without this, it looks like setAttributes() doesn't recognize a change to the array, and the component doesn't re-render.
			 */
			const repeaterRowsCopy = repeaterRows.slice();
			repeaterRowsCopy.splice( index, 1 );

			attr[ parentName ] = repeaterRowsCopy;
			parentBlockProps.setAttributes( attr );
		};
	}

	render() {
		const { rows, fields, parentBlockProps, parentBlock } = this.props;
		const subFields = [];

		for ( let rowIndex in rows ) {
			const rowName     = rows[ rowIndex ];
			const activeClass = this.state.activeFieldset === parseInt( rowIndex ) ? 'active' : ''; // @todo: Make this dynamic.

			const renderedSubField = (
				<BaseControl className={`block-lab-repeater--row ${activeClass}`} key={ `${ rowName }-row` }>
					<Fields
						fields={ fields }
						parentBlockProps={ parentBlockProps }
						parentBlock={ parentBlock }
						rowName={ rowName }
					/>
					<div className="block-lab-repeater--row-actions">
						<IconButton
							key={ `${ rowName }-move-left` }
							icon="arrow-left-alt2"
							label={ __( 'Move left', 'block-lab' ) }
							labelPosition="bottom"
							className="button-move-left"
						/>
						<IconButton
							key={ `${ rowName }-dismiss` }
							icon="dismiss"
							label={ __( 'Remove row', 'block-lab' ) }
							labelPosition="bottom"
							onClick={ this.removeRow( rowIndex ) }
							className="button-dismiss"
						/>
						<IconButton
							key={ `${ rowName }-move-right` }
							icon="arrow-right-alt2"
							label={ __( 'Move right', 'block-lab' ) }
							labelPosition="bottom"
							className="button-move-right"
						/>
					</div>
					<div className="block-lab-repeater__carousel-buttons">
						<IconButton
							key={ `${ rowName }-move-previous` }
							icon="arrow-left-alt2"
							label={ __( 'Previous', 'block-lab' ) }
							labelPosition="bottom"
							className="button-move-left"
							onClick={ () => {
								this.setState( { activeFieldset: this.state.activeFieldset - 1 } );
							} }
						/>
						<IconButton
							key={ `${ rowName }-move-next` }
							icon="arrow-right-alt2"
							label={ __( 'Next', 'block-lab' ) }
							labelPosition="bottom"
							className="button-move-right"
							onClick={ () => {
								this.setState( { activeFieldset: this.state.activeFieldset + 1 } );
							} }
						/>
					</div>
				</BaseControl>
			);
			subFields.push( renderedSubField );
		}

		return subFields;
	}
};

/**
 * Gets the parent from fields, if one exists.
 *
 * Sub-fields in the Repeater control have parents.
 * This looks for a parent in each field, and returns a parent as long as they don't have different parents.
 *
 * @param {Object} fields The fields in which to look for the parent.
 * @return {String|null} parent The parent of the fields.
 */
export const getParent = ( fields ) => {
	let parent = null;
	for ( const field in fields ) {
		if ( fields.hasOwnProperty( field ) ) {
			if ( parent && parent !== fields[ field ].parent ) {
				return null;
			}
			parent = fields[ field ].parent;
		}
	}

	return parent;
};

export const editComponent = ( props, block ) => {
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

/**
 * WordPress dependencies
 */
const { BaseControl, Button, IconButton } = wp.components;
const { Component } = wp.element;
const { __ } = wp.i18n;

/**
 * Internal dependencies
 */
import { Fields } from '../../loader/edit';

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
			activeRow: 0,
		};
	}

	/**
	 * Gets the parent from fields, if one exists.
	 *
	 * Sub-fields in the Repeater control have parents.
	 * This looks for a parent in each field, and returns a parent as long as they don't have different parents.
	 *
	 * @param {Object} fields The fields in which to look for the parent.
	 * @return {String|null} parent The parent of the fields.
	 */
	getParent( fields ) {
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

	/*
	 * On clicking the 'remove' button in a repeater row, this removes it.
	 *
	 * @param {Number} index The index of the row to remove, 0 being the first.
	 */
	removeRow( index ) {
		return () => {
			const { parentBlockProps } = this.props;
			const attr = { ...parentBlockProps.attributes };
			const parentName = this.getParent( this.props.fields );
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
			const activeClass = this.state.activeRow === parseInt( rowIndex ) ? 'active' : ''; // @todo: Make this dynamic.

			const renderedSubField = (
				<BaseControl className={`block-lab-repeater--row ${activeClass}`} key={ `${ rowName }-row` }>
					<Fields
						fields={ fields }
						parentBlockProps={ parentBlockProps }
						parentBlock={ parentBlock }
						rowName={ rowName }
					/>
					<div className="block-lab-repeater--row-actions">
						<Button
							key={ `${ rowName }-move-left` }
							isLink={true}
							className="button-move-left"
						>
							{ __( 'Move left', 'block-lab' ) }
						</Button>
						<span className="separator">|</span>
						<Button
							key={ `${ rowName }-move-right` }
							isLink={true}
							className="button-move-right"
						>
							{ __( 'Move right', 'block-lab' ) }
						</Button>
						<span className="separator">|</span>
						<Button
							key={ `${ rowName }-delete` }
							isLink={true}
							onClick={ this.removeRow( rowIndex ) }
							className="button-dismiss"
						>
							{ __( 'Delete', 'block-lab' ) }
						</Button>
					</div>
					<div className="block-lab-repeater__carousel-buttons">
						<IconButton
							key={ `${ rowName }-move-previous` }
							icon="arrow-left-alt2"
							label={ __( 'Previous', 'block-lab' ) }
							labelPosition="bottom"
							className="button-move-left"
							onClick={ () => {
								var activeRow = this.state.activeRow - 1;
								if ( activeRow < 0 ) {
									activeRow = rows.length - 1;
								}
								this.setState( { activeRow: activeRow } );
							} }
						/>
						<IconButton
							key={ `${ rowName }-move-next` }
							icon="arrow-right-alt2"
							label={ __( 'Next', 'block-lab' ) }
							labelPosition="bottom"
							className="button-move-right"
							onClick={ () => {
								var activeRow = this.state.activeRow + 1;
								if ( activeRow >= rows.length ) {
									activeRow = 0;
								}
								this.setState( { activeRow: activeRow } );
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

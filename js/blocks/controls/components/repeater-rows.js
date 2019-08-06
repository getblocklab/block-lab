/**
 * WordPress dependencies
 */
const { BaseControl, IconButton } = wp.components;
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
								this.setState( { activeRow: this.state.activeRow - 1 } );
							} }
						/>
						<IconButton
							key={ `${ rowName }-move-next` }
							icon="arrow-right-alt2"
							label={ __( 'Next', 'block-lab' ) }
							labelPosition="bottom"
							className="button-move-right"
							onClick={ () => {
								this.setState( { activeRow: this.state.activeRow + 1 } );
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

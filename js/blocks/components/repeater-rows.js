/**
 * WordPress dependencies
 */
const { BaseControl, IconButton } = wp.components;
const { Component, Fragment, createRef } = wp.element;
const { __ } = wp.i18n;
const { getScrollContainer } = wp.dom;

/**
 * Internal dependencies
 */
import { Fields } from './';

/**
 * Gets the repeater rows.
 *
 * @param {Array} rows The repeater rows to render.
 * @param {Array} subFields The fields to render.
 * @param {Object} parentBlockProps The props to pass to the control function.
 * @param {Object} parentBlock The block where the fields are.
 * @return {Array} subFields The rendered sub-fields.
 */
 class RepeaterRows extends Component {

	/**
	 * Constructs the component class.
	 */
	constructor() {
		super( ...arguments );
		this.getParent = this.getParent.bind( this );
		this.removeRow = this.removeRow.bind( this );
		this.move = this.move.bind( this );
		this.getRows = this.getRows.bind( this );

		this.repeaterRows = createRef();

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
	 * @return {string|null} parent The parent of the fields.
	 */
	getParent() {
		const { subFields } = this.props;
		let parent = null;
		for ( const field in subFields ) {
			if ( subFields.hasOwnProperty( field ) ) {
				if ( parent && parent !== subFields[ field ].parent ) {
					return null;
				}
				parent = subFields[ field ].parent;
			}
		}

		return parent;
	};

	/**
	 * On clicking the 'remove' button in a repeater row, this removes it.
	 *
	 * @param {number} index The index of the row to remove, 0 being the first.
	 */
	removeRow( index ) {
		return () => {
			const { parentBlockProps } = this.props;
			const attr = { ...parentBlockProps.attributes };
			const parentName = this.getParent();
			const attribute = attr[ parentName ];
			const repeaterRows = this.getRows( attribute );

			if ( ! repeaterRows ) {
				return;
			}

			/*
			 * Calling slice() essentially creates a copy of repeaterRows.
			 * Without this, it looks like setAttributes() doesn't recognize a change to the array, and the component doesn't re-render.
			 */
			const rows = repeaterRows.slice();
			rows.splice( index, 1 );

			attr[ parentName ] = { rows };
			parentBlockProps.setAttributes( attr );
		};
	}

	/**
	 * On clicking the 'move up' or 'move down' button in a repeater row, this moves it.
	 *
	 * @param {number} from The index of the row to move from.
	 * @param {number} to   The index of the row to move to.
	 */
	move( from, to ) {
		const scrollView = () => {
			// Scroll the view.
			const scrollContainer = getScrollContainer( this.repeaterRows.current );
			const rowRefs = this.repeaterRows.current.querySelectorAll( '.block-lab-repeater--row' );
			const rowRefFrom = rowRefs[ from ];
			const rowRefTo = rowRefs[ to ];
			const scrollTop = scrollContainer.scrollTop + ( rowRefTo.offsetTop - rowRefFrom.offsetTop );

			rowRefTo.classList.add( 'row-to' );
			rowRefFrom.classList.add( 'row-from' );

			setTimeout( () => {
				rowRefTo.classList.remove( 'row-to' );
				rowRefFrom.classList.remove( 'row-from' );
			}, 1000 )

			scrollContainer.scroll( { top: scrollTop, behavior: 'smooth' } );
		}

		return () => {
			const { parentBlockProps } = this.props;
			const attr = { ...parentBlockProps.attributes };
			const parentName = this.getParent();
			const attribute = attr[ parentName ];
			const repeaterRows = this.getRows( attribute );

			/*
			 * Calling slice() essentially creates a copy of repeaterRows.
			 * Without this, it looks like setAttributes() doesn't recognize a change to the array, and the component doesn't re-render.
			 */
			const rows = repeaterRows.slice();

			/*
			 * Ensure that every row has the required attributes, so that we don't lose blank rows.
			 */
			for ( name in this.props.subFields ) {
				rows.forEach( ( row ) => {
					if ( ! row.hasOwnProperty( name ) ) {
						row[ name ] = null;
					}
				} );
			};

			rows.splice(
				to,
				0,
				rows.splice(
					from,
					1
				)[0]
			);

			attr[ parentName ] = { rows };
			parentBlockProps.setAttributes( attr );

			scrollView();
		};
	}

	/**
	 * Gets the rows or a default.
	 *
	 * @param {Object} attribute The block attribute.
	 * @return {Object} The rows.
	 */
	getRows( attribute ) {
		return ( attribute && attribute[ 'rows' ] ) ? attribute[ 'rows' ] : [ {} ];
	}

	/**
	 * Renders the repeater rows.
	 */
	render() {
		const { rows, field, subFields, parentBlockProps, parentBlock } = this.props;

		let upIcon   = 'arrow-up-alt2';
		let downIcon = 'arrow-down-alt2';

		if ( field.columns && '100' !== field.columns ) {
			upIcon   = 'arrow-left-alt2';
			downIcon = 'arrow-right-alt2';
		}

		return (
			<Fragment>
				<div className="block-lab-repeater__rows" ref={this.repeaterRows}>
					{
						rows && rows.map( ( row, rowIndex ) => {
							const activeClass = this.state.activeRow === parseInt( rowIndex ) ? 'active' : ''; // @todo: Make this dynamic.

							return (
								<BaseControl className={ `block-lab-repeater--row ${ activeClass }` } key={ `bl-row-${ rowIndex }` }>
									<div className="block-lab-repeater--row-delete">
									<IconButton
										icon="no"
										key={ `${ rowIndex }-menu` }
										className="button-delete"
										label={ __( 'Delete', 'block-lab' ) }
										onClick={ this.removeRow( rowIndex ) }
										isSmall
									/>
									</div>
									<Fields
										fields={ subFields }
										parentBlockProps={ parentBlockProps }
										parentBlock={ parentBlock }
										rowIndex={ rowIndex }
									/>
									<div className="block-lab-repeater--row-actions">
										<IconButton
											icon={ upIcon }
											key={ `${ rowIndex }-move-up` }
											className="button-move-up"
											label={ __( 'Move up', 'block-lab' ) }
											onClick={ this.move( rowIndex, rowIndex - 1 ) }
											isSmall
										/>
										<IconButton
											icon={ downIcon }
											key={ `${ rowIndex }-move-down` }
											className="button-move-down"
											label={ __( 'Move down', 'block-lab' ) }
											onClick={ this.move( rowIndex, rowIndex + 1 ) }
											isSmall
										/>
									</div>
								</BaseControl>
							);
						} )
					}
					<div className="block-lab-repeater--row block-lab-repeater--row-add">
						<IconButton
							key={ `${ field.name }-repeater-insert` }
							icon="insert"
							label={ __( 'Add new', 'block-lab' ) }
							labelPosition="bottom"
							onClick={ () => {
								const { parentBlockProps } = this.props;
								const attr = { ...parentBlockProps.attributes };
								const withAddedRow = rows.concat( {} );
								attr[ field.name ] = { rows: withAddedRow };
								parentBlockProps.setAttributes( attr );
							} }
							disabled={ false }
						/>
					</div>
				</div>
			</Fragment>
		);
	}
};

export default RepeaterRows;

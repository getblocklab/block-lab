/**
 * WordPress dependendies
 */
const { __ } = wp.i18n;
const { BaseControl, IconButton } = wp.components;

/**
 * Internal dependendies
 */
import { RepeaterRows } from '../components';

const BlockLabRepeaterControl = ( props ) => {
	const { field, getValue, onChange, parentBlock, parentBlockProps } = props;
	const { attributes, setAttributes } = parentBlockProps;
	const attr = { ...attributes };
	const value = attr[ field.name ];
	const defaultRows = [ {} ];
	const hasRows = value && value.hasOwnProperty( 'rows' );
	const rows = hasRows ? value.rows : defaultRows;

	if ( ! hasRows ) {
		onChange( { rows: defaultRows } );
	}
	return (
		<BaseControl className="block-lab-repeater" label={ field.label } help={ field.help }>
			<RepeaterRows
				rows={ rows }
				subFields={ field.sub_fields || defaultRows }
				parentBlockProps={ parentBlockProps }
				parentBlock={ parentBlock }
			/>
			<div className="block-lab-repeater--row-add">
				<IconButton
					key={ `${ field.name }-repeater-insert` }
					icon="insert"
					label={ __( 'Add new', 'block-lab' ) }
					labelPosition="bottom"
					onClick={ () => {
						const withAddedRow = rows.concat( {} );
						attr[ field.name ] = { rows: withAddedRow };
						setAttributes( attr );
					} }
					disabled={ false }
				/>
			</div>
		</BaseControl>
	);
}

export default BlockLabRepeaterControl;

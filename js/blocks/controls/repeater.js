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
	const rows = getValue( props );

	const defaultRows = [ {} ];
	if ( ! rows ) {
		onChange( defaultRows );
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
						const newRows = rows || defaultRows;
						attr[ field.name ] = newRows.concat( {} );
						setAttributes( attr );
					} }
					disabled={ false }
				/>
			</div>
		</BaseControl>
	);
}

export default BlockLabRepeaterControl;

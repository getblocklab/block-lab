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
	const { field, onChange, parentBlock, parentBlockProps } = props;
	const { attributes, setAttributes } = parentBlockProps;
	const attr = { ...attributes };
	const value = attr[ field.name ];
	const defaultRows = [ {} ];
	const hasRows = value && value.hasOwnProperty( 'rows' );
	const rows = hasRows ? value.rows : defaultRows;

	if ( ! hasRows ) {
		onChange( { rows: defaultRows } );
	}

	let className = 'block-lab-repeater';

	if ( field.columns ) {
		className += ' row-width-' + field.columns;
	}

	return (
		<BaseControl className={className} label={ field.label } help={ field.help }>
			<RepeaterRows
				rows={ rows }
				field={ field }
				subFields={ field.sub_fields || defaultRows }
				parentBlockProps={ parentBlockProps }
				parentBlock={ parentBlock }
			/>
		</BaseControl>
	);
}

export default BlockLabRepeaterControl;

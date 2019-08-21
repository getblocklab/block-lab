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
	const { attributes, field, parentBlock, setAttributes } = props;
	const attr = { ...attributes };
	const rows = attr[ field.name ];

	if ( ! rows ) {
		attr[ field.name ] = [];
		setAttributes( attr );
	}

	return (
		<BaseControl className="block-lab-repeater" label={ field.label } help={ field.help }>
			<RepeaterRows
				rows={ attr[ field.name ] }
				subFields={ field.sub_fields || [] }
				parentBlockProps={ props }
				parentBlock={ parentBlock }
			/>
			<IconButton
				key={ `${ field.name }-repeater-insert` }
				icon="insert"
				label={ __( 'Add row', 'block-lab' ) }
				labelPosition="bottom"
				className="block-lab-repeater--add-row"
				onClick={ () => {
					const repeaterRows = rows || [];
					attr[ field.name ] = repeaterRows.concat( {} );
					setAttributes( attr );
				} }
				disabled={ false }
			/>
		</BaseControl>
	);
}

export default BlockLabRepeaterControl;

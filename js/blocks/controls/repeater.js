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
			<div className="block-lab-repeater--row-add">
				<IconButton
					key={ `${ field.name }-repeater-insert` }
					icon="insert"
					label={ __( 'Add new', 'block-lab' ) }
					labelPosition="bottom"
					onClick={ () => {
						const repeaterRows = rows || [];
						attr[ field.name ] = repeaterRows.concat( {} );
						setAttributes( attr );
					} }
					disabled={ false }
				/>
			</div>
		</BaseControl>
	);
}

export default BlockLabRepeaterControl;

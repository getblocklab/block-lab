/**
 * WordPress dependendies
 */
const { __ } = wp.i18n;
const { BaseControl, IconButton } = wp.components;

/**
 * Internal dependendies
 */
import { RepeaterRows } from './components/repeater-rows';

const BlockLabRepeaterControl = ( props, field, block ) => {
	const attr = { ...props.attributes };
	const rows = attr[ field.name ];
	const { setAttributes } = props;

	// @todo: either create a more elegant implementation, or use an existing one.
	const uuid = () => {
		return Math.round( Math.random() * 1000000 );
	};

	return (
		<BaseControl className="block-lab-repeater" label={field.label} help={field.help}>
			<div className="block-lab-repeater--rows">
				<RepeaterRows
					rows={ attr[ field.name ] }
					fields={ field.sub_fields }
					parentBlockProps={ props }
					parentBlock={ block }
				/>
			</div>
			<IconButton
				key={ `${ field.name }-repeater-insert` }
				icon="insert"
				label={ __( 'Add row', 'block-lab' ) }
				labelPosition="bottom"
				onClick={ () => {
					const repeaterRows = rows || [];
					attr[ field.name ] = repeaterRows.concat( uuid() );
					setAttributes( attr );
				} }
				disabled={ false }
			/>
		</BaseControl>
	);
}

export default BlockLabRepeaterControl;

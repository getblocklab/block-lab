/**
 * WordPress dependendies
 */
const { __ } = wp.i18n;
const { BaseControl, IconButton } = wp.components;

/**
 * Internal dependendies
 */
import { RepeaterRows } from '../loader/edit';

const BlockLabRepeaterControl = ( props, field, block ) => {
	const attr = { ...props.attributes };
	const rows = attr[ field.name ];
	const { setAttributes } = props;

	// @todo: either create a more elegant implementation, or use an existing one.
	const uuid = () => {
		return Math.round( Math.random() * 1000000 );
	};

	return (
		<BaseControl className="block-lab-repeater">
			<div className="block-form">
				{ !! field.label && <p className="components-base-control__label">{ field.label }</p> }
				{ !! field.help && <p className="components-base-control__help">{ field.help }</p> }
				<RepeaterRows
					rows={ attr[ field.name ] }
					fields={ field.sub_fields }
					parentBlockProps={ props }
					parentBlock={ block }
				/>
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
			</div>
		</BaseControl>
	);
}

export default BlockLabRepeaterControl;

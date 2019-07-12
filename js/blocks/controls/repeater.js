/**
 * WordPress dependendies
 */
const { BaseControl } = wp.components;

/**
 * Internal dependendies
 */
import { getRenderedFields } from '../loader/edit';

const BlockLabRepeaterControl = ( props, field, block ) => {
	const subFields = field.sub_fields ? getRenderedFields( field.sub_fields, props, block ) : null;

	return (
		<BaseControl className="block-lab-repeater">
			<div class="block-form">
				{ !! field.label && <p className="components-base-control__label">{ field.label }</p> }
				{ !! field.help && <p className="components-base-control__help">{ field.help }</p> }
				{ subFields }
			</div>
		</BaseControl>
	)
}

export default BlockLabRepeaterControl

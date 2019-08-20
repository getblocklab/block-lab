/**
 * Internal dependencies
 */
import { Fields } from './';

/**
 * Gets the form controls for a block.
 *
 * @param {Object} blockProps The block's props.
 * @param {Object} block The block.
 * @return {Function|null} The Edit component for the block.
 */
const FormControls = ( { blockProps, block } ) => {
	const attr = { ...blockProps.attributes };
	const { setAttributes } = blockProps;

	return (
		<div key={ `${ block.name }-fields` } >
			<Fields
				fields={ block.fields }
				parentBlockProps={ blockProps }
				parentBlock={ blockProps }
			/>
		</div>
	)
};

export default FormControls;

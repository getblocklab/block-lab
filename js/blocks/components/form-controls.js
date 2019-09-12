/**
 * Internal dependencies
 */
import { Fields } from './';

/**
 * Gets the form controls for a block.
 *
 * @param {Object} props The props of this components.
 * @param {Object} props.blockProps The block's props.
 * @param {Object} props.block The block.
 * @return {Function|null} The Edit component for the block.
 */
const FormControls = ( { blockProps, block } ) => {
	return (
		<Fields
			key={ `${ block.name }-fields` }
			fields={ block.fields }
			parentBlockProps={ blockProps }
			parentBlock={ blockProps }
		/>
	);
};

export default FormControls;

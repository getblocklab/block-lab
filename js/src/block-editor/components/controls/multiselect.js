/**
 * WordPress dependencies
 */
import { SelectControl } from '@wordpress/components';

const BlockLabMultiselectControl = ( props ) => {
	const { field, getValue, onChange } = props;

	return (
		<SelectControl
			multiple="multiple"
			label={ field.label }
			help={ field.help }
			value={ getValue( props ) || field.default }
			options={ field.options }
			onChange={ onChange }
		/>
	);
};

export default BlockLabMultiselectControl;

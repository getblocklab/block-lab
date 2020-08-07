/**
 * WordPress dependencies
 */
import { ToggleControl } from '@wordpress/components';

const BlockLabToggleControl = ( props ) => {
	const { field, onChange, getValue } = props;
	const initialValue = getValue( props );
	const value =
		'undefined' !== typeof initialValue ? initialValue : field.default;

	return (
		<ToggleControl
			label={ field.label }
			help={ field.help }
			checked={ value }
			onChange={ onChange }
		/>
	);
};

export default BlockLabToggleControl;

/**
 * WordPress dependencies
 */
import { CheckboxControl } from '@wordpress/components';

const BlockLabCheckboxControl = ( props ) => {
	const { field, getValue, onChange } = props;
	let value = getValue( props );
	if ( 'undefined' === typeof value ) {
		value = field.default || false;
	}

	return (
		<CheckboxControl
			label={ field.label }
			help={ field.help }
			checked={ value }
			options={ field.options }
			onChange={ onChange }
		/>
	);
};

export default BlockLabCheckboxControl;

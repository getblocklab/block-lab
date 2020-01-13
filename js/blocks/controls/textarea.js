/**
 * WordPress dependencies
 */
import { TextareaControl } from '@wordpress/components';

const BlockLabTextareaControl = ( props ) => {
	const { getValue, field, onChange } = props;
	const initialValue = getValue( props );
	const value = 'undefined' !== typeof initialValue ? initialValue : field.default;

	return (
		<TextareaControl
			label={ field.label }
			placeholder={ field.placeholder || '' }
			maxLength={ field.maxlength }
			rows={ field.number_rows }
			help={ field.help }
			value={ value }
			onChange={ onChange }
		/>
	);
};

export default BlockLabTextareaControl;

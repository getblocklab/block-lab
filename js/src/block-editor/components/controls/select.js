/**
 * WordPress dependencies
 */
import { SelectControl } from '@wordpress/components';

const BlockLabSelectControl = ( props ) => {
	const { field, getValue, onChange } = props;
	const { __ } = wp.i18n;

	if ( '' === field.default ) {
		field.options = [
			// @see https://github.com/WordPress/gutenberg/issues/11270 Disabled attribute not currently supported.
			{ label: __( '– Select –', 'block-lab' ), value: '', disabled: true },
			...field.options,
		];
	}

	return (
		<SelectControl
			label={ field.label }
			help={ field.help }
			value={ getValue( props ) || field.default }
			options={ field.options }
			onChange={ onChange }
		/>
	);
};

export default BlockLabSelectControl;

const { CheckboxControl } = wp.components;

const BlockLabCheckboxControl = ( props ) => {
	const { field, getValue, onChange } = props
	const attr = { ...props.attributes }
	if ( 'undefined' === typeof attr[ field.name ] ) {
		attr[ field.name ] = field.default || false
	}
	return (
		<CheckboxControl
			label={field.label}
			help={field.help}
			checked={ getValue( props ) }
			options={ field.options }
			onChange={ onChange }
		/>
	);
}

export default BlockLabCheckboxControl;

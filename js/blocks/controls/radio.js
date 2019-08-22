const { RadioControl } = wp.components;

const BlockLabRadioControl = ( props ) => {
	const { field, getValue, onChange } = props;

	return (
		<RadioControl
			label={ field.label }
			help={ field.help }
			selected={ getValue( props ) || field.default }
			options={ field.options }
			onChange={ onChange }
		/>
	);
}

export default BlockLabRadioControl;

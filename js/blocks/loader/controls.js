const {
	CheckboxControl,
	RadioControl,
	RangeControl,
	TextControl,
	TextareaControl,
	ToggleControl,
	SelectControl
} = wp.components;

const getControl = ( props, field ) => {

	const { setAttributes } = props
	const attr = { ...props.attributes }

	switch ( field.control ) {
		case 'text':
			return (
				<TextControl
					label={field.label}
					help=''
					value={attr[ field.name ] || field.default}
					onChange={textControl => {
						attr[ field.name ] = textControl
						setAttributes( attr )
					}}
				/>
			);

		case 'textarea':
			return (
				<TextareaControl
					label={field.label}
					help=''
					value={attr[ field.name ] || field.default}
					onChange={textControl => {
						attr[ field.name ] = textControl
						setAttributes( attr )
					}}
				/>
			);

		case 'checkbox':
			return (
				<p>Checkbox!</p>
			);

		case 'radio':
			return (
				<p>Radio!</p>
			);

		case 'toggle':
			return (
				<p>Toggle!</p>
			);

		case 'select':
			return (
				<p>Select!</p>
			);

		case 'range':
			return (
				<p>Range!</p>
			);

		default:
			return (
				<p>Field {field.name}</p>
			)
	}
};

export { getControl }
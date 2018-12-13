import Color from 'color';

const { __ } = wp.i18n;
const { BaseControl, ColorPicker, Dropdown, Tooltip } = wp.components;

const BlockLabColorPickerControl = ( props, field, block ) => {
	const { setAttributes } = props;
	const attr = { ...props.attributes };
	const customColorPickerLabel = __( 'Custom color picker' );

	// Set the default color, if nothing is choosen.
	attr[ field.name ] = attr[ field.name ] ? attr[ field.name ] : field.default;

	const currentColor = {
		display: 'inline-block',
		width: '120px',
		height: '30px',
		borderRadius: '5px',
	}

	return (
		<BaseControl
			label={ field.label }
			help={ field.help }
			className="block-lab-color-picker"
		>
			<span className="components-color-picker__alpha block-lab-color-picker__current-color" style={ {
				...currentColor,
				overflow: 'hidden',
				border: `1px solid ${ Color( attr[ field.name ] ).darken( 0.2 ) }`,
				marginRight: '15px',
			} }>
				<span style={ {
					...currentColor,
					backgroundColor: attr[ field.name ],
				} }></span>
			</span>
			<Dropdown
				className="components-color-palette__item-wrapper components-color-palette__custom-color block-lab-color-picker__custom-color"
				contentClassName="block-lab-color-picker__picker"
				renderToggle={ ( { isOpen, onToggle } ) => (
					<Tooltip text={ customColorPickerLabel }>
						<button
							type="button"
							aria-expanded={ isOpen }
							className="components-color-palette__item"
							onClick={ onToggle }
							aria-label={ customColorPickerLabel }
						>
							<span className="components-color-palette__custom-color-gradient" />
						</button>
					</Tooltip>
				) }
				renderContent={ () => (
					<ColorPicker
						color={ attr[ field.name ] }
						onChangeComplete={ value => {
							attr[ field.name ] = `rgba( ${ value.rgb.r }, ${ value.rgb.g }, ${ value.rgb.b }, ${ value.rgb.a } )`;
							setAttributes( attr )
						} }
					/>
				) }
			/>
		</BaseControl>
	)
}

export default BlockLabColorPickerControl

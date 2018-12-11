import Color from 'color';

const { __ } = wp.i18n;
const { BaseControl, ColorPicker, Dropdown, Tooltip } = wp.components;

const BlockLabColorPickerControl = ( props, field, block ) => {
	const { setAttributes } = props;
	const attr = { ...props.attributes };
	const customColorPickerLabel = __( 'Custom color picker' );

	// Set a default color of White, if nothing is set.
	attr[ field.name ] = attr[ field.name ] ? attr[ field.name ] : '#fff';

	return (
		<BaseControl
			label={ field.label }
			help={ field.help }
			className="block-lab-color-picker"
		>
			<span class="block-lab-color-picker__current-color" style={ {
				display: 'inline-block',
				width: '120px',
				height: '30px',
				marginRight: '15px',
				borderRadius: '5px',
				border: `1px solid ${ Color( attr[ field.name ] ).darken( 0.2 ) }`,
				backgroundColor: attr[ field.name ],
			} }></span>
			<Dropdown
				className="components-color-palette__item-wrapper components-color-palette__custom-color block-lab-color-picker__custom-color"
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
							attr[ field.name ] = value.hex;
							setAttributes( attr )
						} }
					/>
				) }
			/>
		</BaseControl>
	)
}

export default BlockLabColorPickerControl

const { BaseControl, TextControl, Popover, ColorIndicator, ColorPicker } = wp.components;
const { withState } = wp.compose;

const BlockLabColorPopover = withState( {
} )( ( { isVisible, color, onUpdate, setState } ) => {
	const toggleVisible = () => {
		setState( ( state ) => ( { isVisible: ! state.isVisible } ) );
	};
	const colorChange = ( value ) => {
		let color = value.hex
		if ( value.rgb.a < 1 ) {
			color = 'rgba(' + value.rgb.r + ', ' + value.rgb.g + ', ' + value.rgb.b + ', ' + value.rgb.a + ')'
		}
		setState( () => ( { color: color } ) );
		onUpdate( color )
	};

	return (
		<BaseControl className="block-lab-color-popover">
			<ColorIndicator
				colorValue={color}
				onMouseDown={event => {
					event.preventDefault() // Prevent the popover blur.
				}}
				onClick={toggleVisible}
			>
				{isVisible && (
					<Popover
						onClick={event => {
							event.stopPropagation()
						}}
						onBlur={event => {
							if ( null === event.relatedTarget ) {
								return
							}
							if ( event.relatedTarget.classList.contains( 'wp-block' ) ) {
								toggleVisible()
							}
						}}
					>
						<ColorPicker
							color={color}
							onChangeComplete={value => {
								colorChange( value )
							}}
						/>
					</Popover>
				)}
			</ColorIndicator>
		</BaseControl>
	);
} );

const BlockLabColorControl = ( props, field, block ) => {
	const { setAttributes } = props;
	const attr = { ...props.attributes };

	return (
		<BaseControl label={field.label} className="block-lab-color-control" help={field.help}>
			<TextControl
				defaultValue={field.default}
				value={attr[ field.name ]}
				onClick={(event) => {
					event.target.setSelectionRange(0, event.target.value.length)
				}}
				onChange={colorControl => {
					attr[ field.name ] = colorControl
					setAttributes( attr )
				}}
			/>
			<BlockLabColorPopover
				isVisible={false}
				color={attr[ field.name ]}
				onUpdate={color => {
					attr[ field.name ] = color
					setAttributes( attr )
				}}
			/>
		</BaseControl>
	)
}

export default BlockLabColorControl
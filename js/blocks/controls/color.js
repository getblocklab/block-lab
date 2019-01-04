const { Popover, ColorIndicator, ColorPicker } = wp.components;
const { Fragment } = wp.element;
const { withState } = wp.compose;

const ColorPopover = withState( {
} )( ( { isVisible, color, onUpdate, setState } ) => {
	const toggleVisible = () => {
		setState( ( state ) => ( { isVisible: ! state.isVisible } ) );
	};
	const colorChange = ( value ) => {
		let color = value.hex
		if ( value.rgb.a < 1 ) {
			color = 'rgba(' + value.rgb.r + ',' + value.rgb.g + ',' + value.rgb.b + ',' + value.rgb.a + ')'
		}
		setState( () => ( { color: color } ) );
		onUpdate( color )
	};

	return (
		<ColorIndicator
			colorValue={color}
			onMouseDown={(event) => {
				event.preventDefault() // Prevent the popover blur.
			}}
			onClick={toggleVisible}
		>
			{isVisible && (
				<Popover
					onClick={(event) => {
						event.stopPropagation()
					}}
					onBlur={(event) => {
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
						onChangeComplete={ value => {
							colorChange( value )
						}}
					/>
				</Popover>
			)}
		</ColorIndicator>
	);
} );

const BlockLabColorControl = ( props, field, block ) => {
	const { setAttributes } = props;
	const attr = { ...props.attributes };

	return (
		<Fragment>
			{field.label}
			<ColorPopover
				isVisible={false}
				color={attr[ field.name ]}
				onUpdate={(color) => {
					attr[ field.name ] = color
					setAttributes( attr )
				}}
			/>
		</Fragment>
	)
}

export default BlockLabColorControl
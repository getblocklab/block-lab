/**
 * WordPress dependencies
 */
import { BaseControl, TextControl, Popover, ColorIndicator, ColorPicker } from '@wordpress/components';
import { withState } from '@wordpress/compose';

const BlockLabColorPopover = withState( {
} )( ( { isVisible, color, onUpdate, setState } ) => {
	const toggleVisible = () => {
		setState( ( state ) => ( { isVisible: ! state.isVisible } ) );
	};
	const colorChange = ( value ) => {
		let newColor = value.hex;
		if ( value.rgb.a < 1 ) {
			newColor = 'rgba(' + value.rgb.r + ', ' + value.rgb.g + ', ' + value.rgb.b + ', ' + value.rgb.a + ')';
		}
		setState( () => ( { color: newColor } ) );
		onUpdate( newColor );
	};

	return (
		<BaseControl className="block-lab-color-popover">
			<ColorIndicator
				colorValue={ color }
				onMouseDown={ ( event ) => {
					event.preventDefault(); // Prevent the popover blur.
				} }
				onClick={ toggleVisible }
			>
				{ isVisible && (
					<Popover
						onClick={ ( event ) => {
							event.stopPropagation();
						} }
						onBlur={ ( event ) => {
							if ( null === event.relatedTarget ) {
								return;
							}
							if ( event.relatedTarget.classList.contains( 'wp-block' ) ) {
								toggleVisible();
							}
						} }
					>
						<ColorPicker
							color={ color }
							onChangeComplete={ ( value ) => {
								colorChange( value );
							} }
						/>
					</Popover>
				) }
			</ColorIndicator>
		</BaseControl>
	);
} );

const BlockLabColorControl = ( props ) => {
	const { field, getValue, instanceId, onChange } = props;
	const initialValue = getValue( props );
	const value = 'undefined' !== typeof initialValue ? initialValue : field.default;

	return (
		<BaseControl label={ field.label } id={ `bl-color-${ instanceId }` } className="block-lab-color-control" help={ field.help }>
			<TextControl
				value={ value }
				onChange={ onChange }
			/>
			<BlockLabColorPopover
				isVisible={ false }
				color={ value }
				onUpdate={ onChange }
			/>
		</BaseControl>
	);
};

export default BlockLabColorControl;

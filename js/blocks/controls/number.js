import classNames from 'classnames';

const { TextControl } = wp.components;

const BlockLabNumberControl = ( props ) => {
	const { field, getValue, onChange } = props;

	/**
	 * Sets the Error Class for the Text Control.
	 *
	 * @param {string} element The HTML element.
	 *
	 * @return {void}
	 */
	const setErrorClass = ( element, valid ) => {
		element.className = classNames( 'components-text-control__input', {
			'text-control__error': valid
		} );
	}

	return (
		<TextControl
			type="number"
			label={ field.label }
			placeholder={ field.placeholder || '' }
			help={ field.help }
			defaultValue={ field.default }
			value={ getValue( props ) }
			onChange={ ( numberControl ) => {
				onChange( Number( numberControl ) );
			} }
			onFocus={ event => {
				setErrorClass( document.activeElement, false );
				event.target.reportValidity()
			} }
			onBlur={ event => {
				setErrorClass( event.target, ! event.target.checkValidity() );
			} }
		/>
	);
}

export default BlockLabNumberControl

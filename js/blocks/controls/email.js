import classNames from 'classnames';

const { TextControl } = wp.components;

const BlockLabEmailControl = ( props ) => {
	const { field, getValue, onChange } = props;
	const initialValue = getValue( props );
	const value = 'undefined' !== typeof initialValue ? initialValue : field.default;

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
			type="email"
			label={ field.label }
			placeholder={ field.placeholder || '' }
			help={ field.help }
			value={ value }
			onChange={ onChange }
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

export default BlockLabEmailControl;

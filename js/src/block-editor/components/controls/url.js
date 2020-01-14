/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { TextControl } from '@wordpress/components';

const BlockLabURLControl = ( props ) => {
	const { field, getValue, onChange } = props;
	const initialValue = getValue( props );
	const value = 'undefined' !== typeof initialValue ? initialValue : field.default;

	/**
	 * Sets the Error Class for the Text Control.
	 *
	 * @param {string} element The HTML element.
	 * @param {boolean} valid Whether the value is value.
	 * @return {void}
	 */
	const setErrorClass = ( element, valid ) => {
		element.className = classNames( 'components-text-control__input', {
			'text-control__error': valid,
		} );
	};

	return (
		<TextControl
			type="url"
			label={ field.label }
			placeholder={ field.placeholder || '' }
			help={ field.help }
			value={ value }
			onChange={ onChange }
			onFocus={ ( event ) => {
				setErrorClass( document.activeElement, false );
				event.target.reportValidity();
			} }
			onBlur={ ( event ) => {
				setErrorClass( event.target, ! event.target.checkValidity() );
			} }
		/>
	);
};

export default BlockLabURLControl;

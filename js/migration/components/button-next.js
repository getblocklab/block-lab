// @ts-check

/**
 * External dependencies
 */
import * as React from 'react';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';

/**
 * @typedef ButtonNextProps
 * @property {React.EventHandler<React.MouseEvent<HTMLButtonElement, MouseEvent>>} onClick The click handler.
 * @property {string} [checkboxLabel] The label of the checkbox, if there should be one.
 * @property {number} stepIndex The index of this button's step.
 */

/**
 * The next button.
 *
 * @param {ButtonNextProps} props The component props.
 * @return {React.ReactElement} The component for the step content.
 */
const ButtonNext = ( { onClick, checkboxLabel, stepIndex } ) => {
	const [ isCheckboxChecked, setCheckboxChecked ] = useState( false );

	// If there's no label for the 'confirmation' checkbox, return a simple button.
	if ( ! checkboxLabel ) {
		return <button className="btn" onClick={ onClick }>{ __( 'Next Step', 'block-lab' ) }</button>;
	}

	const inputId = `bl-migration-check-${ stepIndex }`;
	return (
		<>
			<form>
				<input
					id={ inputId }
					type="checkbox"
					onClick={ () => {
						setCheckboxChecked( ! isCheckboxChecked );
					} }
				/>
				<label htmlFor={ inputId } className="ml-2 font-medium">{ checkboxLabel }</label>
			</form>
			<button
				className="btn"
				onClick={ onClick }
				disabled={ ! isCheckboxChecked }
			>
				{ __( 'Next Step', 'block-lab' ) }
			</button>
		</>

	);
};

export default ButtonNext;

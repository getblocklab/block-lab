// @ts-check

/**
 * External dependencies
 */
import * as React from 'react';

/**
 * WordPress dependencies
 */
import { speak } from '@wordpress/a11y';
import apiFetch from '@wordpress/api-fetch';
import { Spinner } from '@wordpress/components';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { ButtonNext, Step, StepContent, StepFooter, StepIcon } from '../';

/**
 * @typedef {Object} InstallActivateGcbProps The component props.
 * @property {React.EventHandler<React.MouseEvent<HTMLButtonElement, MouseEvent>>} goToNext Goes to the next step.
 * @property {boolean} isStepActive Whether this step is active.
 * @property {boolean} isStepComplete Whether this step is complete.
 * @property {number} stepIndex The step index of this step.
 */

/**
 * Installs and activates GCB.
 *
 * @param {InstallActivateGcbProps} Props The component props.
 * @return {React.ReactElement} The component to activate Genesis Custom Blocks.
 */
const InstallActivateGcb = ( { goToNext, isStepActive, isStepComplete, stepIndex } ) => {
	const [ isInProgress, setIsInProgress ] = useState( false );
	const [ isError, setIsError ] = useState( false );
	const [ errorMessage, setErrorMessage ] = useState( '' );
	const [ isSuccess, setIsSuccess ] = useState( false );

	/**
	 * Installs and activates Genesis Custom Blocks.
	 */
	const installAndActivateGcb = async () => {
		speak( __( 'The installation is now in progress', 'block-lab' ) );
		setIsInProgress( true );
		setIsError( false );
		setErrorMessage( '' );

		await apiFetch( {
			path: '/block-lab/install-activate-gcb',
			method: 'POST',
		} ).then( () => {
			speak( __( 'Success! Genesis Custom Blocks is installed and activated.', 'block-lab' ) );
			setIsSuccess( true );
		} ).catch( ( result ) => {
			speak( __( 'The installation and activation failed with the following error:', 'block-lab' ) );
			if ( result.hasOwnProperty( 'message' ) ) {
				speak( result.message );
				setErrorMessage( result.message );
			}
			setIsSuccess( false );
			setIsError( true );
		} );

		setIsInProgress( false );
	};

	return (
		<Step isActive={ isStepActive } isComplete={ isStepComplete }>
			<StepIcon
				index={ stepIndex }
				isComplete={ isStepComplete }
			/>
			<StepContent
				heading={ __( 'Install And Activate Genesis Custom Blocks', 'block-lab' ) }
				isStepActive={ isStepActive }
			>
				{ isInProgress && (
					<>
						<Spinner />
						<p>{ __( 'Installing and activating Genesis Custom Blocks…', 'block-lab' ) }</p>
					</>
				) }
				{ !! errorMessage && (
					<div className="bl-migration__error">
						<p>{ __( 'The following error ocurred:', 'block-lab' ) }</p>
						<p>{ errorMessage }</p>
					</div>
				) }
				{ ! isInProgress && ! isSuccess && (
					<button
						className="btn"
						onClick={ installAndActivateGcb }
					>
						{ isError ? __( 'Try Again', 'block-lab' ) : __( 'Install and activate', 'block-lab' ) }
					</button>
				) }
				{ isSuccess && (
					<>
						<p>{ __( 'Success! Genesis Custom Blocks is installed and activated.', 'block-lab' ) }</p>
						<StepFooter>
							<ButtonNext
								onClick={ goToNext }
								stepIndex={ stepIndex }
							/>
						</StepFooter>
					</>
				) }
			</StepContent>
		</Step>
	);
};

export default InstallActivateGcb;

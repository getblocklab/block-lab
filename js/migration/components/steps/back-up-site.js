// @ts-check

/**
 * External dependencies
 */
import * as React from 'react';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { ButtonNext, ButtonPrevious, Step, StepContent, StepFooter, StepIcon } from '../';
import { FIRST_STEP_NUMBER } from '../../constants';

/**
 * @typedef {Object} BackUpSiteProps The component props.
 * @property {boolean} isStepActive Whether this step is active.
 * @property {boolean} isStepComplete Whether this step is complete.
 * @property {number} stepIndex The step index of this step.
 * @property {React.EventHandler<React.MouseEvent<HTMLButtonElement, MouseEvent>>} goToNext Goes to the next step.
 * @property {React.EventHandler<React.MouseEvent<HTMLButtonElement, MouseEvent>>} goToPrevious Goes to the previous step.
 */

/**
 * The step that prompts to back up the site.
 *
 * @param {BackUpSiteProps} Props The component props.
 * @return {React.ReactElement} The component to prompt to back up the site.
 */
const BackUpSite = ( { isStepActive, isStepComplete, goToNext, goToPrevious, stepIndex } ) => {
	const isFirstStep = FIRST_STEP_NUMBER === stepIndex;

	return (
		<Step isActive={ isStepActive } isComplete={ isStepComplete }>
			<StepIcon
				index={ stepIndex }
				isComplete={ isStepComplete }
			/>
			<StepContent
				heading={ __( 'Back Up Your Site', 'block-lab' ) }
				isStepActive={ isStepActive }
			>
				<p>{ __( 'Migrating from Block Lab to Genesis Custom Blocks is a one-way action. It can’t be undone. Please back up your site before you begin, just in case you need to roll it back.', 'block-lab' ) }</p>
				<StepFooter>
					{ ! isFirstStep && <ButtonPrevious onClick={ goToPrevious } /> }
					<ButtonNext
						checkboxLabel={ __( 'I have backed up my site.', 'block-lab' ) }
						onClick={ goToNext }
						stepIndex={ stepIndex }
					/>
				</StepFooter>
			</StepContent>
		</Step>
	);
};

export default BackUpSite;

/* global blockLabMigration */
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
import { Step, StepContent, StepFooter, StepIcon } from '../';

/**
 * @typedef {Object} ActivateGcbProps The component props.
 * @property {number} currentStepIndex The current step in the migration process.
 * @property {number} stepIndex The step index of this step.
 */

/**
 * The step that prompts to back up the site.
 *
 * @param {ActivateGcbProps} Props The component props.
 * @return {React.ReactElement} The component to prompt to back up the site.
 */
const ActivateGcb = ( { currentStepIndex, stepIndex } ) => {
	const isStepActive = currentStepIndex === stepIndex;
	const isStepComplete = currentStepIndex > stepIndex;

	return (
		<Step isActive={ isStepActive } isComplete={ isStepComplete }>
			<StepIcon
				index={ stepIndex }
				isComplete={ isStepComplete }
			/>
			<StepContent>
				<h3>{ __( 'Activate Genesis Custom Blocks', 'block-lab' ) }</h3>
				<p>
					<span role="img" aria-label={ __( 'party emoji', 'block-lab' ) }>🎉</span>
					&nbsp;
					{ __( 'The migration completed successfully! Time to say goodbye to Block Lab (it’s been fun!) and step into the FUTURE ꜰᴜᴛᴜʀᴇ', 'block-lab' ) }
					&nbsp;
					<sub>{ __( 'FUTURE', 'block-lab' ) }</sub>.
				</p>
				<StepFooter>
					{ /* @ts-ignore */ }
					<a href={ blockLabMigration.activateUrl } className="btn">
						{ __( 'Activate Genesis Custom Blocks', 'block-lab' ) }
					</a>
				</StepFooter>
			</StepContent>
		</Step>
	);
};

export default ActivateGcb;

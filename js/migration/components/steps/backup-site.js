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
import { ButtonNext, Step, StepContent, StepFooter, StepIcon } from '../';

/**
 * @typedef {Object} BackupSiteProps The component props.
 * @property {number} currentStepIndex The current step in the migration process.
 * @property {number} stepIndex The step index of this step.
 * @property {React.MouseEventHandler} goToNext Goes to the next step.
 */

/**
 * The step that prompts to back up the site.
 *
 * @param {BackupSiteProps} Props The component props.
 * @return {React.ReactElement} The component to prompt to back up the site.
 */
const BackupSite = ( { currentStepIndex, stepIndex, goToNext } ) => {
	const isStepActive = currentStepIndex === stepIndex;
	const isStepComplete = currentStepIndex > stepIndex;

	return (
		<Step isActive={ isStepActive } isComplete={ isStepComplete }>
			<StepIcon
				index={ stepIndex }
				isComplete={ isStepComplete }
			/>
			<StepContent>
				<h3 className="font-semibold mt-1">{ __( 'Backup your site', 'block-lab' ) }</h3>
				<p>{ __( 'Migrating from Block Lab to Genesis Custom Blocks is a one-way action. It can’t be undone. Please backup your site before you begin, just in case you need to roll it back.', 'block-lab' ) }</p>
				<StepFooter>
					<ButtonNext
						checkboxLabel={ __( 'I have backed up my site.', 'block-lab' ) }
						onClick={ goToNext }
					/>
				</StepFooter>
			</StepContent>
		</Step>
	);
};

export default BackupSite;

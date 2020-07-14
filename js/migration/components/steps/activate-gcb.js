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
 * @property {boolean} isStepActive Whether this step is active.
 * @property {boolean} isStepComplete Whether this step is complete.
 * @property {number} stepIndex The step index of this step.
 */

/**
 * The step that prompts to back up the site.
 *
 * @param {ActivateGcbProps} Props The component props.
 * @return {React.ReactElement} The component to activate Genesis Custom Blocks.
 */
const ActivateGcb = ( { isStepActive, isStepComplete, stepIndex } ) => {
	return (
		<Step isActive={ isStepActive } isComplete={ isStepComplete }>
			<StepIcon
				index={ stepIndex }
				isComplete={ isStepComplete }
			/>
			<StepContent
				heading={ __( 'Activate Genesis Custom Blocks', 'block-lab' ) }
				isStepActive={ isStepActive }
			>
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

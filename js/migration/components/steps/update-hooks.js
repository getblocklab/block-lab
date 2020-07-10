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

/**
 * @typedef {Object} UpdateHooksProps The component props.
 * @property {boolean} isStepActive Whether this step is active.
 * @property {boolean} isStepComplete Whether this step is complete.
 * @property {number} stepIndex The step index of this step.
 * @property {React.EventHandler<React.MouseEvent<HTMLButtonElement, MouseEvent>>} goToNext Goes to the next step.
 * @property {React.EventHandler<React.MouseEvent<HTMLButtonElement, MouseEvent>>} goToPrevious Goes to the next step.
 */

/**
 * The step that prompts to back up the site.
 *
 * @param {UpdateHooksProps} Props The component props.
 * @return {React.ReactElement} The component to prompt to back up the site.
 */
const UpdateHooks = ( { isStepActive, isStepComplete, stepIndex, goToNext, goToPrevious } ) => {
	// @todo: replace this.
	const hooksDetailsUrl = 'https://example.com/';
	const phpApiDetailsUrl = 'https://developer.wpengine.com/genesis-custom-blocks/functions/';

	return (
		<Step isActive={ isStepActive } isComplete={ isStepComplete }>
			<StepIcon
				index={ stepIndex }
				isComplete={ isStepComplete }
			/>
			<StepContent>
				<h3 className="font-semibold mt-1">{ __( 'Update Hooks & API', 'block-lab' ) }</h3>
				<p>{ __( 'In most cases, you won’t have to worry about this step. However, there are some instances that will require manual edits to your custom block related files. These are:', 'block-lab' ) }</p>
				<ul className="list-disc list-inside mt-2">
					<li>
						<b>{ __( 'Hooks', 'block-lab' ) }</b> - { __( 'The Block Lab hook names have changed. If you’ve extended Block Lab with custom functionality using these, you’ll need to make some small changes.', 'block-lab' ) }
						&nbsp;
						<a
							href={ hooksDetailsUrl }
							target="_blank"
							rel="noopener noreferrer"
						>
							{ __( 'More details here.', 'block-lab' ) }
						</a>
					</li>
					<li>
						<b>{ __( 'API', 'block-lab' ) }</b> - { __( 'If you use Block Lab’s PHP API or JSON API to register & configure your custom blocks, you’ll need to make some small changes.', 'block-lab' ) }
						&nbsp;
						<a
							href={ phpApiDetailsUrl }
							target="_blank"
							rel="noopener noreferrer"
						>
							{ __( 'More details here.', 'block-lab' ) }
						</a>
					</li>
				</ul>
				<StepFooter>
					<ButtonPrevious onClick={ goToPrevious } />
					<ButtonNext
						checkboxLabel={ __( "I'm all ok on the Hooks & API front.", 'block-lab' ) }
						onClick={ goToNext }
						stepIndex={ stepIndex }
					/>
				</StepFooter>
			</StepContent>
		</Step>
	);
};

export default UpdateHooks;

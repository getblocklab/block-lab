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
 * @typedef StepIconProps
 * @property {number} index The index of this icon's step.
 * @property {boolean} isComplete Whether this icon's step is active.
 */

/**
 * The icon of the step number.
 *
 * @param {StepIconProps} props The component props.
 * @return {React.ReactElement} props The icon component.
 */
const StepIcon = ( { index, isComplete } ) => {
	const titleId = `bl-migration-icon-${ index }`;
	const svg = (
		<svg fill="currentColor" viewBox="0 0 20 20" aria-labelledby={ titleId }>
			<path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd"></path>
			<title id={ titleId }>{ __( 'Step completed', 'block-lab' ) }</title>
		</svg>
	);

	return (
		<div className="step-icon">
			{ isComplete ? svg : index }
		</div>
	);
};

export default StepIcon;

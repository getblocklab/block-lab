// @ts-check

/**
 * External dependencies
 */
import * as React from 'react';

/**
 * @typedef StepContentProps
 * @property {React.ReactNode} children The component's children.
 */

/**
 * The content of the step.
 *
 * @param {StepContentProps} props The component props.
 * @return {React.ReactElement} The component for the step content.
 */
const StepContent = ( { children } ) => {
	return (
		<div className="step-content">
			{ children }
		</div>
	);
};

export default StepContent;

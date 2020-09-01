// @ts-check

/**
 * External dependencies
 */
import * as React from 'react';

/**
 * @typedef StepFooterProps
 * @property {React.ReactNode} children The component's children.
 */

/**
 * The footer of the step.
 *
 * @param {StepFooterProps} props The component props.
 * @return {React.ReactElement} The component for the step content.
 */
const StepFooter = ( { children } ) => {
	return (
		<div className="step-footer">
			{ children }
		</div>
	);
};

export default StepFooter;

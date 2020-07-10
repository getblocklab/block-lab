// @ts-check

/**
 * External dependencies
 */
import classNames from 'classnames';
import * as React from 'react';

/**
 * @typedef StepProps
 * @property {boolean} isActive Whether this step is active.
 * @property {boolean} isComplete Whether this step is complete.
 * @property {React.ReactNode} children The children of the component.
 */

/**
 * Migration step.
 *
 * @param {StepProps} props The component props.
 */
const Step = ( { isActive, isComplete, children } ) => {
	return (
		<div
			className={ classNames( 'step', {
				'step--active': isActive,
				'step--complete': isComplete,
			} ) }
		>
			{ children }
		</div>
	);
};

export default Step;

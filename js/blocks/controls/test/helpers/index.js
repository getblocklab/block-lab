/**
 * External dependencies
 */
import { render } from '@testing-library/react';

/**
 * Renders a control, and gets utility methods to test it.
 *
 * @param {Function} Control The control to render.
 * @param {Object} props The props for the control.
 * @return {Object} The control and testing utility functions.
 */
export const setupControl = ( Control, props ) => {
	const { field } = props;
	const utils = render(
		<Control
			{ ...props }
			getValue={ jest.fn() }
		/>
	);
	const control = utils.getByLabelText( field.label );
	return {
		control,
		...utils,
	};
};

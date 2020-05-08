/**
 * External dependencies
 */
import '@testing-library/jest-dom/extend-expect';
import { render } from '@testing-library/react';
import user from '@testing-library/user-event';

/**
 * Internal dependencies
 */
import BlockLabCheckboxControl from '../checkbox';

test( 'checkbox control', () => {
	const field = {
		help: 'Here is help text for the checkbox field',
		default: '1',
	};
	const mockOnChange = jest.fn();
	const { getByRole, getByText } = render(
		<BlockLabCheckboxControl
			field={ field }
			getValue={ jest.fn() }
			onChange={ mockOnChange }
		/>
	);
	const checkbox = getByRole( 'checkbox' );

	getByText( field.help );
	expect( checkbox ).toBeChecked( !! field.default );

	// Click the bock to uncheck it, and verify that false is sent to the onChange handler.
	user.click( checkbox );
	expect( mockOnChange ).toHaveBeenCalledWith( false );
} );

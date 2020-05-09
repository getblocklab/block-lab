/**
 * External dependencies
 */
import { render } from '@testing-library/react';
import user from '@testing-library/user-event';

/**
 * Internal dependencies
 */
import BlockLabCheckboxControl from '../checkbox';

test( 'checkbox control', async () => {
	const field = {
		help: 'Here is help text for the checkbox field',
		default: '1',
	};
	const mockOnChange = jest.fn();
	const { findByRole, findByText } = render(
		<BlockLabCheckboxControl
			field={ field }
			getValue={ jest.fn() }
			onChange={ mockOnChange }
		/>
	);
	const checkbox = await findByRole( 'checkbox' );

	await findByText( field.help );
	expect( checkbox ).toBeChecked( !! field.default );

	// Click the bock to uncheck it, and verify that false is sent to the onChange handler.
	user.click( checkbox );
	expect( mockOnChange ).toHaveBeenCalledWith( false );
} );

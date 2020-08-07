/**
 * External dependencies
 */
import { render } from '@testing-library/react';
import user from '@testing-library/user-event';

/**
 * Internal dependencies
 */
import BlockLabRadioControl from '../radio';

test( 'radio control', async () => {
	const firstOption = 'first';
	const secondOption = 'second';
	const options = [ { value: firstOption }, { value: secondOption } ];
	const field = {
		options,
		label: 'This is a label for the radio field',
		help: 'Here is some help text for the radio field',
		default: secondOption,
	};
	const mockOnChange = jest.fn();

	const { getAllByRole, getByText } = render(
		<BlockLabRadioControl
			field={ field }
			getValue={ jest.fn() }
			onChange={ mockOnChange }
		/>
	);

	// The second radio option should be checked, as it's the default.
	expect( await getAllByRole( 'radio' )[ 1 ] ).toBeChecked();
	getByText( field.help );
	getByText( field.label );

	user.click( getAllByRole( 'radio' )[ 0 ] );
	expect( mockOnChange ).toHaveBeenCalledWith( firstOption );
} );

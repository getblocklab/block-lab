/**
 * External dependencies
 */
import '@testing-library/jest-dom/extend-expect';
import { render } from '@testing-library/react';
import user from '@testing-library/user-event';

/**
 * Internal dependencies
 */
import BlockLabColorControl from '../color';

test( 'color control', () => {
	const field = {
		default: '#bef5cb',
		help: 'This is some help text',
		label: 'This is an example label',
	};
	const mockOnChange = jest.fn();
	const { getByText, getByRole } = render(
		<BlockLabColorControl
			field={ field }
			getValue={ jest.fn() }
			onChange={ mockOnChange }
			instanceId="7e8f32c1-f1dd-3151"
		/>
	);
	const input = getByRole( 'textbox' );

	expect( input.value ).toBe( field.default );
	getByText( field.help );
	getByText( field.label );

	// On entering a new color, it should be sent to the onChange handler.
	const enteredColor = '#fff';
	user.clear( input );
	user.type( input, enteredColor );
	expect( mockOnChange ).toHaveBeenCalledWith( enteredColor );
} );

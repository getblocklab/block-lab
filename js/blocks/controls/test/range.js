/**
 * External dependencies
 */
import { render, fireEvent } from '@testing-library/react';

/**
 * Internal dependencies
 */
import BlockLabRangeControl from '../range';

test( 'range control', () => {
	const field = {
		label: 'This is a label for the Range field',
		help: 'This is help text for the range',
		min: 4,
		max: 100,
		step: 2,
		default: 50,
	};
	const mockOnChange = jest.fn();

	const { getAllByLabelText, getByText } = render(
		<BlockLabRangeControl
			field={ field }
			getValue={ jest.fn() }
			onChange={ mockOnChange }
		/>
	);
	const control = getAllByLabelText( field.label )[ 0 ];

	getByText( field.help );

	// This should have the default value as its value.
	expect( control ).toHaveAttribute( 'value', field.default.toString() );

	// Changing the value should call the onChange handler.
	const newValue = 58;
	fireEvent.change( control, { target: { value: newValue } } );
	expect( mockOnChange ).toHaveBeenCalledWith( newValue );
} );

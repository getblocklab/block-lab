/**
 * External dependencies
 */
import '@testing-library/jest-dom/extend-expect';
import { render, fireEvent } from '@testing-library/react';

/**
 * Internal dependencies
 */
import BlockLabCheckboxControl from '../checkbox';

const field = {
	help: 'Here is help text for the checkbox field',
	default: '1',
};
const mockOnChange = jest.fn();
const setup = () => {
	const utils = render(
		<BlockLabCheckboxControl
			field={ field }
			getValue={ jest.fn() }
			onChange={ mockOnChange }
		/>
	);
	const checkbox = document.querySelector( 'input' );
	return {
		checkbox,
		...utils,
	};
};

describe( 'Checkbox', () => {
	it( 'displays the default value if there is no entered value', () => {
		const { checkbox } = setup();
		expect( checkbox.value ).toBe( field.default );
	} );

	it( 'has the help text', () => {
		const { getByText } = setup();
		expect( getByText( field.help ) ).toBeInTheDocument();
	} );

	it.each( 'sends a new value to the onChange when the checkbox is clicked', () => {
		const newValue = '0';
		const { checkbox } = setup();
		fireEvent.change( checkbox, { target: { value: newValue } } );
		expect( mockOnChange ).toHaveBeenCalledWith( newValue );
	} );
} );

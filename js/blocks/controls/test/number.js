/**
 * External dependencies
 */
import '@testing-library/jest-dom/extend-expect';
import { render, fireEvent } from '@testing-library/react';

/**
 * Internal dependencies
 */
import BlockLabNumberControl from '../number';

const field = {
	label: 'This is a label for the number field',
	default: '52344',
	placeholder: 'This is a placeholder for the number',
};
const mockOnChange = jest.fn();
const setup = () => {
	const utils = render(
		<BlockLabNumberControl
			field={ field }
			getValue={ jest.fn() }
			onChange={ mockOnChange }
		/>
	);
	const input = utils.getByLabelText( field.label );
	return {
		input,
		...utils,
	};
};

describe( 'Number', () => {
	it( 'displays the default value if no value is entered', () => {
		const { input } = setup();
		expect( input.value ).toBe( field.default );
	} );

	it( 'has the placeholder', () => {
		const { getByPlaceholderText } = setup();
		expect( getByPlaceholderText( field.placeholder ) ).toBeInTheDocument();
	} );

	it.each( [
		0,
		352343,
		9523342951313513414,
	] )( 'sends a number to the onChange handler when it is entered',
		( enteredText ) => {
			const { input } = setup();
			fireEvent.change( input, { target: { value: enteredText } } );
			expect( mockOnChange ).toHaveBeenCalledWith( enteredText );
		}
	);
} );

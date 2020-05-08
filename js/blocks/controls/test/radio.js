/**
 * External dependencies
 */
import { render, fireEvent } from '@testing-library/react';

/**
 * Internal dependencies
 */
import BlockLabRadioControl from '../radio';

const firstOption = 'first';
const secondOption = 'second';
const options = [
	{ value: firstOption },
	{ value: secondOption },
];
const field = {
	options,
	label: 'This is a label for the radio field',
	help: 'Here is some help text for the radio field',
	default: secondOption,
};
const mockOnChange = jest.fn();
const setup = () => {
	const utils = render(
		<BlockLabRadioControl
			field={ field }
			getValue={ jest.fn() }
			onChange={ mockOnChange }
		/>
	);
	const radio = document.querySelector( 'input' );
	return {
		radio,
		...utils,
	};
};

describe( 'Radio', () => {
	it( 'displays the default value if there is no entered value', () => {
		setup();
		expect( document.querySelector( `[value="${ field.default }"` ).hasAttribute( 'checked' ) ).toStrictEqual( true );
	} );

	it( 'has the help text', () => {
		const { getByText } = setup();
		expect( getByText( field.help ) ).toBeInTheDocument();
	} );

	it( 'has the label', () => {
		const { getByText } = setup();
		expect( getByText( field.label ) ).toBeInTheDocument();
	} );

	it.each( 'sends a new value to the onChange when the radio is changed', () => {
		const { radio } = setup();
		fireEvent.change( radio, { target: { value: firstOption } } );
		expect( mockOnChange ).toHaveBeenCalledWith( firstOption );
	} );
} );

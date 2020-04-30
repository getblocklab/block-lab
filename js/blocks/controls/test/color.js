/**
 * External dependencies
 */
import '@testing-library/jest-dom/extend-expect';
import { fireEvent, render } from '@testing-library/react';

/**
 * Internal dependencies
 */
import BlockLabColorControl from '../color';

const field = {
	default: '#bef5cb',
	help: 'This is some help text',
	label: 'This is an example label',
};
const mockOnChange = jest.fn();
const setup = () => {
	const utils = render(
		<BlockLabColorControl
			field={ field }
			getValue={ jest.fn() }
			onChange={ mockOnChange }
			instanceId="7e8f32c1-f1dd-3151"
		/>
	);

	const input = document.querySelector( 'input' );
	return {
		input,
		...utils,
	};
};

describe( 'Color', () => {
	it( 'has the default value at first', () => {
		const { input } = setup();
		expect( input.value ).toBe( field.default );
	} );

	it( 'has the help text', () => {
		const { getByText } = setup();
		expect( getByText( field.help ) ).toBeInTheDocument();
	} );

	it( 'has the label text', () => {
		const { getByText } = setup();
		expect( getByText( field.help ) ).toBeInTheDocument();
	} );

	it( 'sends a value entered in the text input to the onChange handler', () => {
		const enteredColor = '#fff';
		const { input } = setup();
		fireEvent.change( input, { target: { value: enteredColor } } );
		expect( mockOnChange ).toHaveBeenCalledWith( enteredColor );
	} );
} );

/**
 * External dependencies
 */
import '@testing-library/jest-dom/extend-expect';
import { fireEvent, render, waitFor } from '@testing-library/react';

/**
 * Internal dependencies
 */
import BlockLabColorControl from '../color';

const field = {
	label: 'This is an example label',
	default: '#bef5cb',
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

	it( 'sends a value entered in the text input to the onChange handler', () => {
		const enteredColor = '#fff';
		const { input } = setup();
		fireEvent.change( input, { target: { value: enteredColor } } );
		expect( mockOnChange ).toHaveBeenCalledWith( enteredColor );
	} );

	it( 'sends a value entered in the color popover to the onChange handler', () => {
		const newColor = '#afdefd';
		const { getByLabelText, input } = setup();

		fireEvent.click( document.querySelector( '.component-color-indicator' ) );
		fireEvent.change( getByLabelText( 'Color value in hexadecimal' ), { target: { value: newColor } } );
		waitFor( () => {
			expect( input.value ).toStrictEqual( newColor );
		} );
	} );
} );

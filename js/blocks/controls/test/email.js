/**
 * External dependencies
 */
import '@testing-library/jest-dom/extend-expect';
import React from 'react';
import { fireEvent, render } from '@testing-library/react';

/**
 * Internal dependencies
 */
import BlockLabEmailControl from '../email';

const label = 'text-label';
const defaultValue = 'example';
const mockOnChange = jest.fn();
const setup = () => {
	const utils = render(
		<BlockLabEmailControl
			field={ { label, default: defaultValue } }
			getValue={ jest.fn() }
			onChange={ mockOnChange }
		/>
	);
	const input = utils.getByLabelText( label );
	return {
		input,
		...utils,
	};
};

describe( 'Email', () => {
	it( 'displays the default value if no value is entered', () => {
		const { input } = setup();
		expect( input.value ).toBe( defaultValue );
	} );

	it.each( [
		'you@example.com',
		'not-a-valid-email',
		')$@$%*)#$*@)#$',
	] )( 'should send any entered text to the onChange handler, even if it is not a valid email',
		( enteredText ) => {
			const { input } = setup();
			fireEvent.change( input, { target: { value: enteredText } } );
			expect( mockOnChange ).toHaveBeenCalledWith( enteredText );
		}
	);

	it.each( [
		true,
		false,
	] )( 'should have an invalid class if the event object finds it is invalid',
		( isInputValid ) => {
			const { input } = setup();
			const mockEvent = { target: { checkValidity: jest.fn() } };
			mockEvent.target.checkValidity.mockReturnValueOnce( isInputValid );
			fireEvent.blur( input, mockEvent );
			expect( input.classList.contains( 'text-control__error' ) ).toStrictEqual( ! isInputValid );
		}
	);
} );

/**
 * External dependencies
 */
import '@testing-library/jest-dom/extend-expect';
import { fireEvent } from '@testing-library/react';

/**
 * Internal dependencies
 */
import BlockLabEmailControl from '../email';
import { setupControl } from './helpers';

const field = {
	label: 'text-label',
	default: 'This is an example default value',
};
const props = {
	field,
	onChange: jest.fn(),
};

describe( 'Email', () => {
	it( 'displays the default value if no value is entered', () => {
		const { control } = setupControl( BlockLabEmailControl, props );
		expect( control.value ).toBe( field.default );
	} );

	it.each( [
		'you@example.com',
		'not-a-valid-email',
		')$@$%*)#$*@)#$',
	] )( 'should send any entered text to the onChange handler, even if it is not a valid email',
		( enteredText ) => {
			const { control } = setupControl( BlockLabEmailControl, props );
			fireEvent.change( control, { target: { value: enteredText } } );
			expect( props.onChange ).toHaveBeenCalledWith( enteredText );
		}
	);

	it.each( [
		true,
		false,
	] )( 'should have an invalid class if the event object finds it is invalid',
		( isInputValid ) => {
			const { control } = setupControl( BlockLabEmailControl, props );
			const mockEvent = { target: { checkValidity: jest.fn() } };
			mockEvent.target.checkValidity.mockReturnValueOnce( isInputValid );
			fireEvent.blur( control, mockEvent );
			expect( control.classList.contains( 'text-control__error' ) ).toStrictEqual( ! isInputValid );
		}
	);
} );

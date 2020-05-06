/**
 * External dependencies
 */
import '@testing-library/jest-dom/extend-expect';
import { fireEvent } from '@testing-library/react';

/**
 * Internal dependencies
 */
import BlockLabURLControl from '../url';
import { setupControl } from './helpers';

const field = {
	label: 'This is an example label',
	default: 'https://example.com/here-is-something',
};
const mockOnChange = jest.fn();
const props = { field, mockOnChange };

describe( 'Url', () => {
	it( 'displays the default value if no value is entered', () => {
		const { control } = setupControl( BlockLabURLControl, props );
		expect( control.value ).toBe( field.default );
	} );

	it( 'sends the text that is entered to the onChange handler', () => {
		const { control } = setupControl( BlockLabURLControl, props );
		const enteredUrl = 'https://example.com/baz';
		fireEvent.change( control, { target: { value: enteredUrl } } );
		expect( mockOnChange ).toHaveBeenCalledWith( enteredUrl );
	} );

	it.each( [
		true,
		false,
	] )( 'should have an invalid class if the event object finds it is invalid',
		( isInputValid ) => {
			const { control } = setupControl( BlockLabURLControl, props );
			const mockEvent = { target: { checkValidity: jest.fn() } };
			mockEvent.target.checkValidity.mockReturnValueOnce( isInputValid );
			fireEvent.blur( control, mockEvent );
			expect( control.classList.contains( 'text-control__error' ) ).toStrictEqual( ! isInputValid );
		}
	);
} );

/**
 * External dependencies
 */
import { fireEvent } from '@testing-library/react';

/**
 * Internal dependencies
 */
import BlockLabURLControl from '../url';
import { setupControl } from './helpers';

/**
 * Gets the testing props.
 *
 * @return {Object} Testing props.
 */
const getProps = () => ( {
	field: {
		label: 'This is an example label',
		default: 'https://example.com/here-is-something',
	},
	onChange: jest.fn(),
} );

describe( 'url control', () => {
	it( 'displays the default value if no value is entered', () => {
		const props = getProps();
		const { control } = setupControl( BlockLabURLControl, props );

		expect( control.value ).toBe( props.field.default );
	} );

	it( 'sends the text that is entered to the onChange handler', () => {
		const props = getProps();
		const { control } = setupControl( BlockLabURLControl, props );
		const enteredUrl = 'https://example.com/baz';
		fireEvent.change( control, { target: { value: enteredUrl } } );

		expect( props.onChange ).toHaveBeenCalledWith( enteredUrl );
	} );

	it.each( [
		true,
		false,
	] )( 'should have an invalid class if the event object finds it is invalid',
		( isInputValid ) => {
			const props = getProps();
			const { control } = setupControl( BlockLabURLControl, props );
			const mockEvent = { target: { checkValidity: jest.fn() } };
			mockEvent.target.checkValidity.mockReturnValueOnce( isInputValid );
			fireEvent.blur( control, mockEvent );

			expect( control.classList.contains( 'text-control__error' ) ).toStrictEqual( ! isInputValid );
		}
	);
} );

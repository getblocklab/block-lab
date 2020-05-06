/**
 * External dependencies
 */
import '@testing-library/jest-dom/extend-expect';
import { fireEvent } from '@testing-library/react';

/**
 * Internal dependencies
 */
import BlockLabTextareaControl from '../textarea';
import { setupControl } from './helpers';

const field = {
	label: 'This is an example label',
	default: 'Here is a default value',
	placeholder: 'This is a placeholder for the Textarea',
};
const props = {
	field,
	onChange: jest.fn(),
};

describe( 'Textarea', () => {
	it( 'displays the default value if no value is entered', () => {
		const { control } = setupControl( BlockLabTextareaControl, props );
		expect( control.value ).toBe( field.default );
	} );

	it( 'has the placeholder', () => {
		const { getByPlaceholderText } = setupControl( BlockLabTextareaControl, props );
		expect( getByPlaceholderText( field.placeholder ) ).toBeInTheDocument();
	} );

	it.each( [
		'Some entered text',
		'っていった',
		'987654321',
		'This is long text that is entered into a textarea, it keeps going longer than one might normally type',
	] )( 'Any text entered is sent to the onChange handler',
		( enteredText ) => {
			const { control } = setupControl( BlockLabTextareaControl, props );
			fireEvent.change( control, { target: { value: enteredText } } );
			expect( props.onChange ).toHaveBeenCalledWith( enteredText );
		}
	);
} );

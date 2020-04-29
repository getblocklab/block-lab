/**
 * External dependencies
 */
import '@testing-library/jest-dom/extend-expect';
import { render, fireEvent } from '@testing-library/react';

/**
 * Internal dependencies
 */
import BlockLabTextareaControl from '../textarea';

const field = {
	label: 'This is an example label',
	default: 'Here is a default value',
	placeholder: 'This is a placeholder for the Textarea',
};
const mockOnChange = jest.fn();
const setup = () => {
	const utils = render(
		<BlockLabTextareaControl
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

describe( 'Textarea', () => {
	it( 'displays the default value if no value is entered', () => {
		const { input } = setup();
		expect( input.value ).toBe( field.default );
	} );

	it( 'has the placeholder', () => {
		const { getByPlaceholderText } = setup();
		expect( getByPlaceholderText( field.placeholder ) ).toBeInTheDocument();
	} );

	it.each( [
		'Some entered text',
		'っていった',
		'987654321',
		'This is long text that is entered into a textarea, it keeps going longer than one might normally type',
	] )( 'Any text entered is sent to the onChange handler',
		( enteredText ) => {
			const { input } = setup();
			fireEvent.change( input, { target: { value: enteredText } } );
			expect( mockOnChange ).toHaveBeenCalledWith( enteredText );
		}
	);
} );

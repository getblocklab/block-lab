/**
 * External dependencies
 */
import '@testing-library/jest-dom/extend-expect';
import { fireEvent, render } from '@testing-library/react';

/**
 * WordPress dependencies
 */
import { Popover } from '@wordpress/components';

/**
 * Internal dependencies
 */
import BlockLabColorControl from '../textarea';

const field = {
	label: 'This is an example label',
	default: '#bef5cb',
	placeholder: 'This is a color field',
};
const mockOnChange = jest.fn();
const setup = () => {
	const utils = render(
		<>
			<Popover.Slot />
			<div>
				<BlockLabColorControl
					field={ field }
					getValue={ jest.fn() }
					onChange={ mockOnChange }
				/>
			</div>
		</>
	);
	const control = utils.getByLabelText( field.label );
	return {
		control,
		...utils,
	};
};

describe( 'Color', () => {
	it( 'has the default value at first', () => {
		const { control } = setup();
		expect( control.value ).toBe( field.default );
	} );

	it( 'has the placeholder', () => {
		const { getByPlaceholderText } = setup();
		expect( getByPlaceholderText( field.placeholder ) ).toBeInTheDocument();
	} );

	it.each( 'sends a value entered in the text input to the onChange handler',
		( enteredColor ) => {
			const { control } = setup();
			fireEvent.change( control, { target: { value: enteredColor } } );
			expect( mockOnChange ).toHaveBeenCalledWith( enteredColor );
		}
	);
} );

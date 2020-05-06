/**
 * External dependencies
 */
import '@testing-library/jest-dom/extend-expect';
import { fireEvent } from '@testing-library/react';

/**
 * Internal dependencies
 */
import BlockLabNumberControl from '../number';
import { setupControl } from './helpers';

const field = {
	label: 'This is a label for the number field',
	default: '52344',
	placeholder: 'This is a placeholder for the number',
};
const props = {
	field,
	onChange: jest.fn(),
};

describe( 'Number', () => {
	it( 'displays the default value if no value is entered', () => {
		const { control } = setupControl( BlockLabNumberControl, props );
		expect( control.value ).toBe( field.default );
	} );

	it( 'has the placeholder', () => {
		const { getByPlaceholderText } = setupControl( BlockLabNumberControl, props );
		expect( getByPlaceholderText( field.placeholder ) ).toBeInTheDocument();
	} );

	it.each( [
		0,
		352343,
		9523342951313513414,
	] )( 'sends a number to the onChange handler when it is entered',
		( enteredText ) => {
			const { control } = setupControl( BlockLabNumberControl, props );
			fireEvent.change( control, { target: { value: enteredText } } );
			expect( props.onChange ).toHaveBeenCalledWith( enteredText );
		}
	);
} );

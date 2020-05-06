/**
 * External dependencies
 */
import '@testing-library/jest-dom/extend-expect';
import { fireEvent } from '@testing-library/react';

/**
 * Internal dependencies
 */
import BlockLabTextControl from '../text';
import { setupControl } from './helpers';

const field = {
	label: 'Here is an example label',
	default: 'This is a default value',
};
const mockOnChange = jest.fn();
const props = { field, mockOnChange };

describe( 'Text', () => {
	it( 'displays the default value if no value is entered', () => {
		const { control } = setupControl( BlockLabTextControl, props );
		expect( control.value ).toBe( field.default );
	} );

	it.each( [
		'Example text',
		'942',
		'#$ Special %()@$ characters @${}[]',
		'Very long text that keeps going on and on and on and it continues longer than you would normally expect',
	] )( 'Any text entered is sent to the onChange handler',
		( enteredText ) => {
			const { control } = setupControl( BlockLabTextControl, props );
			fireEvent.change( control, { target: { value: enteredText } } );
			expect( mockOnChange ).toHaveBeenCalledWith( enteredText );
		}
	);
} );

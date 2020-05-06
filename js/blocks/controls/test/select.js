/**
 * External dependencies
 */
import '@testing-library/jest-dom/extend-expect';
import { fireEvent } from '@testing-library/react';

/**
 * Internal dependencies
 */
import BlockLabSelectControl from '../select';
import { setupControl } from './helpers';

const firstValue = 'first';
const secondValue = 'second';
const field = {
	label: 'Here is an example label',
	help: 'This is some help text',
	default: firstValue,
	options: [
		{
			label: 'First',
			value: firstValue,
		},
		{
			label: 'Second',
			value: secondValue,
		},
	],
};
const mockOnChange = jest.fn();
const props = { field, mockOnChange };

describe( 'Select', () => {
	it( 'has the help text', () => {
		const { getByText } = setupControl( BlockLabSelectControl, props );
		expect( getByText( field.help ) ).toBeInTheDocument();
	} );

	it( 'sends the new value to the onChange handler', () => {
		const { control } = setupControl( BlockLabSelectControl, props );
		fireEvent.change( control, { target: { value: secondValue } } );
		expect( mockOnChange ).toHaveBeenCalledWith( secondValue );
	} );
} );

/**
 * External dependencies
 */
import '@testing-library/jest-dom/extend-expect';
import { render, fireEvent } from '@testing-library/react';

/**
 * Internal dependencies
 */
import BlockLabRangeControl from '../range';
const field = {
	label: 'This is a label for the Range field',
	help: 'This is help text for the range',
	min: 4,
	max: 100,
	step: 2,
	default: 50,
};
const mockOnChange = jest.fn();
const setup = () => {
	const utils = render(
		<BlockLabRangeControl
			field={ field }
			getValue={ jest.fn() }
			onChange={ mockOnChange }
		/>
	);
	const control = utils.getAllByLabelText( field.label )[ 0 ];
	return {
		control,
		...utils,
	};
};

describe( 'Range', () => {
	it( 'has the help text', () => {
		const { getByText } = setup();
		expect( getByText( field.help ) ).toBeInTheDocument();
	} );

	it( 'has the default value', () => {
		const { control } = setup();
		expect( control.value ).toStrictEqual( field.default.toString() );
	} );

	it( 'sends the new value to the onChange handler', () => {
		const { control } = setup();
		const newValue = 58;
		fireEvent.change( control, { target: { value: newValue } } );
		expect( mockOnChange ).toHaveBeenCalledWith( newValue );
	} );
} );

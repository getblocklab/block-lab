/**
 * External dependencies
 */
import '@testing-library/jest-dom/extend-expect';
import { render, fireEvent } from '@testing-library/react';

/**
 * Internal dependencies
 */
import BlockLabSelectControl from '../select';

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
const setup = () => {
	const utils = render(
		<BlockLabSelectControl
			field={ field }
			getValue={ jest.fn() }
			onChange={ mockOnChange }
		/>
	);
	const select = utils.getByLabelText( field.label );
	return {
		select,
		...utils,
	};
};

describe( 'Select', () => {
	it( 'has the help text', () => {
		const { getByText } = setup();
		expect( getByText( field.help ) ).toBeInTheDocument();
	} );

	it( 'sends the new value to the onChange handler', () => {
		const { select } = setup();
		fireEvent.change( select, { target: { value: secondValue } } );
		expect( mockOnChange ).toHaveBeenCalledWith( secondValue );
	} );
} );

/**
 * External dependencies
 */
import '@testing-library/jest-dom/extend-expect';
import { render, fireEvent } from '@testing-library/react';

/**
 * Internal dependencies
 */
import BlockLabMultiselectControl from '../multiselect';

const firstValue = 'foo';
const secondValue = 'baz';
const field = {
	label: 'Here is a multiselect label',
	help: 'Here is some help text',
	default: [ firstValue ],
	options: [
		{
			label: 'Foo',
			value: firstValue,
		},
		{
			label: 'Baz',
			value: secondValue,
		},
	],
};
const mockOnChange = jest.fn();
const setup = () => {
	const utils = render(
		<BlockLabMultiselectControl
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

describe( 'Multiselect', () => {
	it( 'has the label', () => {
		const { getByLabelText } = setup();
		expect( getByLabelText( field.label ) ).toBeInTheDocument();
	} );

	it( 'has the help text', () => {
		const { getByText } = setup();
		expect( getByText( field.help ) ).toBeInTheDocument();
	} );

	it( 'sends the new value to the onChange handler', () => {
		const { select } = setup();
		fireEvent.change( select, { target: { value: [ secondValue ] } } );
		expect( mockOnChange ).toHaveBeenCalledWith( [ secondValue ] );
	} );
} );

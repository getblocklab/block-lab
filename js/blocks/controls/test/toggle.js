/**
 * External dependencies
 */
import '@testing-library/jest-dom/extend-expect';
import { fireEvent, render } from '@testing-library/react';

/**
 * Internal dependencies
 */
import BlockLabToggleControl from '../toggle';
const field = {
	label: 'Here is an example label',
	help: 'And here is help text',
	default: 1,
};
const mockOnChange = jest.fn();
const setup = () => {
	const utils = render(
		<BlockLabToggleControl
			field={ field }
			getValue={ jest.fn() }
			onChange={ mockOnChange }
		/>
	);
	return {
		...utils,
	};
};

describe( 'Toggle', () => {
	it( 'has the label text', () => {
		const { getByLabelText } = setup();
		expect( getByLabelText( field.label ) ).toBeInTheDocument();
	} );

	it( 'has the help text', () => {
		const { getByText } = setup();
		expect( getByText( field.help ) ).toBeInTheDocument();
	} );

	it( 'sends the new value to the onChange handler', () => {
		setup();
		fireEvent.click( document.querySelector( 'input' ) );

		// Because it used to be truthy, clicking the toggle should make it false.
		expect( mockOnChange ).toHaveBeenCalledWith( false );

		fireEvent.click( document.querySelector( 'input' ) );

		// Clicking it again should toggle it back to true.
		expect( mockOnChange ).toHaveBeenCalledWith( true );
	} );
} );

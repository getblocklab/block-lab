/**
 * External dependencies
 */
import { render } from '@testing-library/react';

/**
 * Internal dependencies
 */
import BlockLabToggleControl from '../toggle';

test( 'toggle control', () => {
	const field = {
		label: 'Here is an example label',
		help: 'And here is help text',
		default: 1,
	};
	const mockOnChange = jest.fn();

	const { getByLabelText, getByText } = render(
		<BlockLabToggleControl
			field={ field }
			getValue={ jest.fn() }
			onChange={ mockOnChange }
		/>
	);

	expect( getByLabelText( field.label ) ).toBeInTheDocument();
	expect( getByText( field.help ) ).toBeInTheDocument();
} );

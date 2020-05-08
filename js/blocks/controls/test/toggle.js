/**
 * External dependencies
 */
import { render } from '@testing-library/react';

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
} );

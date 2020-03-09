/**
 * External dependencies
 */
import '@testing-library/jest-dom/extend-expect';
import React from 'react';
import { render, fireEvent } from '@testing-library/react';

/**
 * Internal dependencies
 */
import BlockLabTextControl from '../text';

const label = 'text-label';
const defaultValue = 'example';
const mockOnChange = jest.fn();
const setup = () => {
	const utils = render(
		<BlockLabTextControl
			field={ { label, default: defaultValue } }
			getValue={ jest.fn() }
			onChange={ mockOnChange }
		/>
	);
	const input = utils.getByLabelText( label );
	return {
		input,
		...utils,
	};
};

describe( 'Text', () => {
	it( 'displays the default value if no value is entered', () => {
		const { input } = setup();
		expect( input.value ).toBe( defaultValue );
	} );

	it.each( [
		'Example text',
		'942',
		'#$ Special %()@$ characters @${}[]',
		'Very long text that keeps going on and on and on and it continues longer than you would normally expect',
	] )( 'Any text entered is sent to the onChange handler',
		( enteredText ) => {
			const { input } = setup();
			fireEvent.change( input, { target: { value: enteredText } } );
			expect( mockOnChange ).toHaveBeenCalledWith( enteredText );
		}
	);
} );

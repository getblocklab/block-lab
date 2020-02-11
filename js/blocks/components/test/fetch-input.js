/**
 * External dependencies
 */
import '@testing-library/jest-dom/extend-expect';
import React from 'react';
import { fireEvent, render, screen, waitForDomChange } from '@testing-library/react';

/**
 * WordPress dependencies
 */
import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import FetchInput from '../fetch-input';

jest.mock( '@wordpress/api-fetch' );

const label = 'text-label';
const value = 'this is a value';
const displayValue = 'example-display-value';
const mockOnChange = jest.fn();
const setup = ( props ) => {
	const utils = render(
		<FetchInput { ...props } />
	);
	const input = utils.getByLabelText( label );
	return {
		input,
		...utils,
	};
};
const baseProps = {
	value,
	displayValue,
	field: { label },
	onChange: mockOnChange,
	getValueFromAPI: ( fromAPi ) => fromAPi,
	getDisplayValueFromAPI: ( fromApi ) => fromApi,
};

describe( 'FetchInput', () => {
	it( 'displays the value if there is no displayValue', () => {
		const { input } = setup( { value, field: { label }, onChange: mockOnChange } );
		expect( input.value ).toBe( value );
	} );

	it( 'displays the displayValue instead of the value if both are present', () => {
		const { input } = setup( { value, displayValue, field: { label }, onChange: mockOnChange } );
		expect( input.value ).toBe( displayValue );
	} );

	it( 'displays the popover when there are search results to show', async () => {
		const exampleResult = 'this-is-a-result';
		const results = [ exampleResult ];
		apiFetch.mockImplementationOnce( () => new Promise( ( resolve ) => resolve( results ) ) );
		const { input } = setup( baseProps );
		fireEvent.focus( input );

		const suggestion = await screen.findByText( exampleResult );
		expect( suggestion ).not.toBe( null );
	} );

	it.each( [
		[ [], true ],
		[ [ 'a-result' ], false ],
		[ [ 'first-result', 'another-result' ], false ],
	] )( 'should only have the error class if there are no results after focusing',
		( apiResults, expected ) => {
			apiFetch.mockImplementationOnce( () => new Promise( ( resolve ) => resolve( apiResults ) ) );
			const { input } = setup( baseProps );
			fireEvent.focus( input );

			waitForDomChange( { container: input } ).then( () => {
				expect( input.classList.contains( 'text-control__error' ) ).toStrictEqual( expected );
			} );
		}
	);
} );

/**
 * External dependencies
 */
import { fireEvent } from '@testing-library/react';

/**
 * Internal dependencies
 */
import BlockLabMultiselectControl from '../multiselect';
import { setupControl } from './helpers';

test( 'multiselect control', () => {
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
	const props = {
		field,
		onChange: jest.fn(),
	};
	const { control, getByText } = setupControl(
		BlockLabMultiselectControl,
		props
	);

	getByText( field.help );
	fireEvent.change( control, { target: { value: [ secondValue ] } } );

	expect( props.onChange ).toHaveBeenCalledWith( [ secondValue ] );
} );

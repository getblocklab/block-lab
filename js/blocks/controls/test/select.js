/**
 * External dependencies
 */
import { waitFor } from '@testing-library/react';
import user from '@testing-library/user-event';

/**
 * Internal dependencies
 */
import BlockLabSelectControl from '../select';
import { setupControl } from './helpers';

test( 'select control', async () => {
	const firstValue = 'first';
	const secondValue = 'second';
	const secondLabel = 'Second';
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
				label: secondLabel,
				value: secondValue,
			},
		],
	};
	const props = {
		field,
		onChange: jest.fn(),
	};

	const { findByRole, findByText } = setupControl(
		BlockLabSelectControl,
		props
	);
	await findByText( field.help );
	const control = await findByRole( 'combobox' );

	// This should send the new value to the onChange handler.
	user.selectOptions( control, secondValue );
	//fireEvent.change( control, { target: { value: secondValue } } );
	await waitFor( () =>
		expect( props.onChange ).toHaveBeenCalledWith( secondValue )
	);
} );

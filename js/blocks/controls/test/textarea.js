/**
 * External dependencies
 */
import { fireEvent } from '@testing-library/react';

/**
 * Internal dependencies
 */
import BlockLabTextareaControl from '../textarea';
import { setupControl } from './helpers';

/**
 * Gets the testing props.
 *
 * @return {Object} Testing props.
 */
const getProps = () => ( {
	field: {
		label: 'This is an example label',
		default: 'Here is a default value',
		placeholder: 'This is a placeholder for the Textarea',
	},
	onChange: jest.fn(),
} );

describe( 'textarea control', () => {
	it( 'displays the default value if no value is entered', () => {
		const props = getProps();
		const { control } = setupControl( BlockLabTextareaControl, props );

		expect( control.value ).toBe( props.field.default );
	} );

	it( 'has the placeholder', () => {
		const props = getProps();
		const { getByPlaceholderText } = setupControl(
			BlockLabTextareaControl,
			props
		);

		expect(
			getByPlaceholderText( props.field.placeholder )
		).toBeInTheDocument();
	} );

	it.each( [
		'Some entered text',
		'っていった',
		'987654321',
		'This is long text that is entered into a textarea, it keeps going longer than one might normally type',
	] )(
		'Any text entered is sent to the onChange handler',
		( enteredText ) => {
			const props = getProps();
			const { control } = setupControl( BlockLabTextareaControl, props );
			fireEvent.change( control, { target: { value: enteredText } } );

			expect( props.onChange ).toHaveBeenCalledWith( enteredText );
		}
	);
} );

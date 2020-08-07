/**
 * External dependencies
 */
import { fireEvent } from '@testing-library/react';

/**
 * Internal dependencies
 */
import BlockLabTextControl from '../text';
import { setupControl } from './helpers';

/**
 * Gets the testing props.
 *
 * @return {Object} Testing props.
 */
const getProps = () => ( {
	field: {
		label: 'Here is an example label',
		default: 'This is a default value',
	},
	onChange: jest.fn(),
} );

describe( 'text control', () => {
	it( 'displays the default value if no value is entered', () => {
		const props = getProps();
		const { control } = setupControl( BlockLabTextControl, props );

		expect( control ).toHaveAttribute( 'value', props.field.default );
	} );

	it.each( [
		'Example text',
		'942',
		'#$ Special %()@$ characters @${}[]',
		'Very long text that keeps going on and on and on and it continues longer than you would normally expect',
	] )(
		'Any text entered is sent to the onChange handler',
		( enteredText ) => {
			const props = getProps();
			const { control } = setupControl( BlockLabTextControl, props );
			fireEvent.change( control, { target: { value: enteredText } } );

			expect( props.onChange ).toHaveBeenCalledWith( enteredText );
		}
	);
} );

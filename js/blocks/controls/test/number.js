/**
 * External dependencies
 */
import { fireEvent } from '@testing-library/react';

/**
 * Internal dependencies
 */
import BlockLabNumberControl from '../number';
import { setupControl } from './helpers';

/**
 * Gets the props for the component.
 *
 * @return {Object} The props.
 */
const getProps = () => ( {
	field: {
		label: 'This is a label for the number field',
		default: '52344',
		placeholder: 'This is a placeholder for the number',
	},
	onChange: jest.fn(),
} );

describe( 'number control', () => {
	it( 'displays the default value if no value is entered', () => {
		const props = getProps();
		const { control } = setupControl( BlockLabNumberControl, props );
		expect( control ).toHaveAttribute( 'value', props.field.default );
	} );

	it( 'has the placeholder', async () => {
		const props = getProps();
		const { findByPlaceholderText } = setupControl( BlockLabNumberControl, props );
		expect( await findByPlaceholderText( props.field.placeholder ) ).toBeInTheDocument();
	} );

	it.each( [
		0,
		352343,
		9523342951313513414,
	] )( 'sends a number to the onChange handler when it is entered',
		( enteredText ) => {
			const props = getProps();
			const { control } = setupControl( BlockLabNumberControl, props );
			fireEvent.change( control, { target: { value: enteredText } } );
			expect( props.onChange ).toHaveBeenCalledWith( enteredText );
		}
	);
} );

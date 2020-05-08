/**
 * External dependencies
 */
import { render } from '@testing-library/react';

/**
 * Internal dependencies
 */
import BlockLabImageControl from '../image';

/**
 * Gets the props for the tested component.
 *
 * @return {Object} The props to pass to the component.
 */
const getProps = () => ( {
	field: {
		label: 'This is an example label',
		help: 'This is some help text',
		default: 'https://example.com/image.jpg',
	},
	getValue: jest.fn(),
	onChange: jest.fn(),
	instanceId: '85811934-a952-4cc1-8fca-01b825c11cfe',
} );

test( 'image control', () => {
	const props = getProps();
	const { getByText } = render( <BlockLabImageControl { ...props } /> );

	expect( getByText( props.field.label ) ).toBeInTheDocument();
	expect( getByText( props.field.help ) ).toBeInTheDocument();
} );

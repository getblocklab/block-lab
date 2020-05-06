/**
 * External dependencies
 */
import '@testing-library/jest-dom/extend-expect';
import { getByText, render } from '@testing-library/react';

/**
 * Internal dependencies
 */
import BlockLabImageControl from '../image';

const props = {
	field: {
		label: 'This is an example label',
		help: 'This is some help text',
		default: 'https://example.com/image.jpg',
	},
	getValue: jest.fn(),
	onChange: jest.fn(),
	instanceId: '85811934-a952-4cc1-8fca-01b825c11cfe',
};

describe( 'Image', () => {
	it( 'has the label', () => {
		const { getByLabelText } = render( <BlockLabImageControl { ...props } /> );
		expect( getByLabelText( props.field.label ) ).toBeInTheDocument();
	} );
	it( 'has the help text', () => {
		const { debug } = render( <BlockLabImageControl { ...props } /> );
		debug();
		expect( getByText( document, props.field.help ) ).toBeInTheDocument();
	} );
} );

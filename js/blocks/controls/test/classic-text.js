/**
 * External dependencies
 */
import '@testing-library/jest-dom/extend-expect';
import { getByText, render } from '@testing-library/react';

/**
 * Internal dependencies
 */
import BlockLabClassicTextControl from '../classic-text';

const props = {
	field: {
		label: 'This is an example label',
		help: 'This is some help text',
		default: 'This is a default value',
	},
	getValue: jest.fn(),
	onChange: jest.fn(),
	rowIndex: 1,
	parentBlockProps: {
		clientId: '85811934-a952-4cc1-8fca-01b825c11cfe',
	},
};

global.wp = {
	oldEditor: {
		initialize: jest.fn(),
		remove: jest.fn(),
	},
};
window.wpEditorL10n = {
	tinymce: {
		baseURL: 'https://example.com',
		suffix: 'baz',
	},
};
window.tinymce = {
	EditorManager: {
		overrideDefaults: jest.fn(),
	},
};

describe( 'ClassicText', () => {
	it( 'has the help text', () => {
		render( <BlockLabClassicTextControl { ...props } /> );
		expect( getByText( document, props.field.help ) ).toBeInTheDocument();
	} );
} );

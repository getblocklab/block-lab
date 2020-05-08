/**
 * External dependencies
 */
import '@testing-library/jest-dom/extend-expect';
import { render } from '@testing-library/react';

/**
 * Internal dependencies
 */
import BlockLabClassicTextControl from '../classic-text';

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

test( 'classic text control', async () => {
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

	const { findByText } = render( <BlockLabClassicTextControl { ...props } /> );
	const control = await findByText( props.field.help );

	expect( control ).toBeInTheDocument();
} );

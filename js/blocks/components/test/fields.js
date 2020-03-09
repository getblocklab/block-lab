/**
 * External dependencies
 */
import '@testing-library/jest-dom/extend-expect';
import React from 'react';
import { render, screen } from '@testing-library/react';

/**
 * Internal dependencies
 */
import Fields from '../fields';

const helpText = 'This is help text';

describe( 'Fields', () => {
	it( 'does not display a control that is supposed to be in the Inspector Controls', () => {
		render(
			<Fields
				fields={ [ {
					name: 'example_email',
					help: helpText,
					location: 'inspector',
					control: 'email',
				} ] }
				parentBlockProps={ {} }
			/>
		);

		expect( screen.queryByText( helpText ) ).toBeNull();
	} );

	it( 'displays a control that is supposed to be in the editor', () => {
		render(
			<Fields
				fields={ [ {
					name: 'example_email',
					help: helpText,
					location: 'editor',
					control: 'email',
				} ] }
				parentBlockProps={ {} }
			/>
		);

		expect( screen.getByText( helpText ) ).toBeInTheDocument();
	} );

	it( 'has a class name based on the width', () => {
		render(
			<Fields
				fields={ [ {
					name: 'example_email',
					width: '50',
					help: helpText,
					location: 'editor',
					control: 'email',
				} ] }
				parentBlockProps={ {} }
			/>
		);

		const classWithWidth = 'width-50';
		expect( document.body.getElementsByClassName( classWithWidth )[ 0 ] ).not.toBeNull();
	} );
} );

/**
 * External dependencies
 */
import { fireEvent, getByText, render, waitFor } from '@testing-library/react';
import user from '@testing-library/user-event';

/**
 * WordPress dependencies
 */
import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import BlockLabPostControl from '../post';

jest.mock( '@wordpress/api-fetch' );

/**
 * Gets testing props for the component.
 *
 * @return {Object} The props.
 */
const getProps = () => ( {
	field: {
		label: 'Here is an example label',
		help: 'And here is some help text',
		post_type_rest_slug: 'posts',
	},
	getValue: jest.fn(),
	onChange: jest.fn(),
	instanceId: '243256134-e891-4ba1-8fca-01b825c11cfe',
} );

test( 'post control', async () => {
	const props = getProps();
	const { findByRole, findByText, findByLabelText } = render(
		<BlockLabPostControl { ...props } />
	);
	await findByLabelText( props.field.label );
	await findByText( props.field.help );

	const input = await findByRole( 'combobox' );
	const post = {
		id: 921,
		title: {
			rendered: 'Here Is An Example Post',
		},
	};

	// Mock the API fetch function that gets the posts.
	apiFetch.mockImplementationOnce(
		() => new Promise( ( resolve ) => resolve( [ post ] ) )
	);

	// Focus the <input>, so the popover appears with post suggestion(s).
	user.click( input );

	// Click to select a post.
	await waitFor( () =>
		fireEvent.click( getByText( document, post.title.rendered ) )
	);

	// The onChange handler should be called with the selected post.
	expect( props.onChange ).toHaveBeenCalledWith( {
		id: post.id,
		name: post.title.rendered,
	} );
} );

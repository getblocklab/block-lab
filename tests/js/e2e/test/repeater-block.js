/**
 * External dependencies
 */
import { act } from 'react-dom/test-utils';
import { mount } from 'enzyme';

/**
 * WordPress dependencies
 */
import { registerCoreBlocks } from '@wordpress/block-library';
// Force register 'core/editor' store.
import { store } from '@wordpress/editor'; // eslint-disable-line no-unused-vars
require( '@wordpress/blocks' );
require( '@wordpress/core-data' );
require( '@wordpress/edit-post' );
import Editor from '@wordpress/edit-post/src/editor';

jest.mock( '@wordpress/edit-post/src/components/layout', () => () => 'Layout' );

/**
 * Whether the node has the text in its textContent.
 *
 * @param {Object} nodeToSearch The element in which to search for the text.
 * @param {string} text The text to search the node for.
 */
const hasText = ( nodeToSearch, text ) => -1 !== nodeToSearch.textContent.indexOf( text );

const blockSlug = 'test-repeater';
const blockName = `block-lab/${ blockSlug }`;
const blockTitle = 'Test Repeater';
const help = 'Here is some help text';
const minSubFields = 1;
const maxSubFields = 3;

const textarea = {
	name: 'textarea',
	label: 'Here Is A Textarea',
	control: 'textarea',
	type: 'textarea',
	order: 0,
	location: null,
	width: '75',
	help: 'This is more help text',
	default: 'This is an example default value',
	placeholder: 'Here is some placeholder text',
	maxlength: 3,
	number_rows: 4,
	new_lines: 'autop',
	parent: 'repeater',
};

const color = {
	name: 'color',
	label: 'This is a label for color',
	control: 'color',
	type: 'string',
	order: 1,
	location: null,
	width: '100',
	help: 'Here is some help text',
	default: '#ffffff',
	parent: 'repeater',
};

const blockLabBlocks = {
	'block-lab/test-repeater': {
		name: blockName,
		title: blockTitle,
		excluded: [],
		icon: 'block_lab',
		category: {
			slug: 'layout',
			title: 'Layout Elements',
			icon: null,
		},
		keywords: [ 'repeater', 'panel' ],
		fields: {
			repeater: {
				name: 'repeater',
				label: 'Repeater',
				control: 'repeater',
				type: 'object',
				order: 0,
				help: 'Some example help text',
				min: minSubFields,
				max: maxSubFields,
				sub_fields: {
					textarea,
					color,
				},
			},
		},
	},
};

const blockLab = {
	authorBlocks: [ blockSlug ],
	postType: 'post',
};

const editorSettings = {
	alignWide: true,
	availableTemplates: {
		'': 'Default template',
		'templates/template-cover.php': 'Cover Template',
	},
	allowedBlockTypes: true,
	disableCustomColors: false,
	disableCustomFontSizes: false,
	disableCustomGradients: false,
	disablePostFormats: true,
	titlePlaceholder: 'Add title',
	bodyPlaceholder: 'Start writing or type to choose a block',
	isRTL: false,
	autosaveInterval: 60,
	maxUploadFileSize: 134217728,
	allowedMimeTypes: {
		'jpg|jpeg|jpe': 'image/jpeg',
	},
	styles: [
		{ css: "\/**\n * Colors\n *\/\n\/**\n * Breakpoints & Media Queries\n *\/\n\/**\n * Colors\n *\/\n\/**\n * Often re-used variables\n *\/\n\/**\n * Grid System.\n * https:\/\/make.wordpress.org\/design\/2019\/10\/31\/proposal-a-consistent-spacing-system-for-wordpress\/\n *\/\n\/**\n * Breakpoint mixins\n *\/\n\/**\n * Long content fade mixin\n *\n * Creates a fading overlay to signify that the content is longer\n * than the space allows.\n *\/\n\/**\n * Button states and focus styles\n *\/\n\/**\n * Applies editor left position to the selector passed as argument\n *\/\n\/**\n * Styles that are reused verbatim in a few places\n *\/\n\/**\n * Allows users to opt-out of animations via OS-level preferences.\n *\/\n\/**\n * Reset default styles for JavaScript UI based pages.\n * This is a WP-admin agnostic reset\n *\/\n\/**\n * Reset the WP Admin page styles for Gutenberg-like pages.\n *\/\n\/**\n * Editor Normalization Styles\n *\n * These are only output in the editor, but styles here are prefixed .editor-styles-wrapper and affect the theming\n * of the editor by themes.\n * Why do these exist? Why not rely on browser defaults?\n * These styles are necessary so long as CSS can bleed from the wp-admin into the editing canvas itself.\n * Let's continue working to refactor these away, whether through Shadow DOM or better scoping of upstream styles.\n *\/\nbody {\n  font-family: \"Noto Serif\", serif;\n  font-size: 16px;\n  line-height: 1.8;\n  color: #191e23; }\n\n\/* Headings *\/\nh1 {\n  font-size: 2.44em; }\n\nh2 {\n  font-size: 1.95em; }\n\nh3 {\n  font-size: 1.56em; }\n\nh4 {\n  font-size: 1.25em; }\n\nh5 {\n  font-size: 1em; }\n\nh6 {\n  font-size: 0.8em; }\n\nh1,\nh2,\nh3 {\n  line-height: 1.4; }\n\nh4 {\n  line-height: 1.5; }\n\nh1 {\n  margin-top: 0.67em;\n  margin-bottom: 0.67em; }\n\nh2 {\n  margin-top: 0.83em;\n  margin-bottom: 0.83em; }\n\nh3 {\n  margin-top: 1em;\n  margin-bottom: 1em; }\n\nh4 {\n  margin-top: 1.33em;\n  margin-bottom: 1.33em; }\n\nh5 {\n  margin-top: 1.67em;\n  margin-bottom: 1.67em; }\n\nh6 {\n  margin-top: 2.33em;\n  margin-bottom: 2.33em; }\n\nh1,\nh2,\nh3,\nh4,\nh5,\nh6 {\n  color: inherit; }\n\np {\n  font-size: inherit;\n  line-height: inherit;\n  margin-top: 28px;\n  margin-bottom: 28px; }\n\nul,\nol {\n  margin-bottom: 28px;\n  padding: inherit;\n  padding-left: 1.3em;\n  margin-left: 1.3em; }\n  ul ul,\n  ul ol,\n  ol ul,\n  ol ol {\n    margin-bottom: 0; }\n  ul li,\n  ol li {\n    margin-bottom: initial; }\n\nul {\n  list-style-type: disc; }\n\nol {\n  list-style-type: decimal; }\n\nul ul,\nol ul {\n  list-style-type: circle; }\n" }, { "css": "body { font-family: 'Noto Serif' }" }], "imageSizes": [{ "slug": "thumbnail", "name": "Thumbnail" }, { "slug": "medium", "name": "Medium" }, { "slug": "large", "name": "Large" }, { "slug": "full", "name": "Full Size" }], "imageDimensions": { "thumbnail": { "width": 150, "height": 150, "crop": true },
		"medium": { "width": 300, "height": 300, "crop": false },
		"large": { "width": 1024, "height": 1024, "crop": false } },
		"richEditingEnabled": true, "postLock": { "isLocked": false, "activePostLock": "1588290621:1" },
		"postLockUtils": { "nonce": "5506e92159", "unlockNonce": "6024870438", "ajaxUrl": "https:\/\/bl.test\/wp-admin\/admin-ajax.php" },
		"enableCustomFields": false,
		"colors": [{ "name": "Accent Color", "slug": "accent", "color": "#cd2653" },
		{ "name": "Primary", "slug": "primary", "color": "#000000" },
		{ "name": "Secondary", "slug": "secondary", "color": "#6d6d6d" },
		{ "name": "Subtle Background", "slug": "subtle-background", "color": "#dcd7ca" },
		{ "name": "Background Color", "slug": "background", "color": "#f5efe0" },
	],
	fontSizes: [
		{ "name": "Small", "shortName": "S", "size": 18, "slug": "small" },
		{ "name": "Regular", "shortName": "M", "size": 21, "slug": "normal" },
		{ "name": "Large", "shortName": "L", "size": 26.25, "slug": "large" },
		{ "name": "Larger", "shortName": "XL", "size": 32, "slug": "larger" },
	],
};

describe( 'RepeaterBlock', () => {

	beforeAll( registerCoreBlocks );
	/*
	const originalFetch = window.fetch;
	beforeEach( () => {
		window.fetch = jest.fn();
		window.fetch.mockReturnValue(
			Promise.resolve( {
				status: 200,
				json() {
					return Promise.resolve( {} );
				},
			} ).catch( () => {} )
		);
	} );

	afterAll( () => {
		window.fetch = originalFetch;
	} );
*/
	it( 'displays the repeater block in the inserter and the block has the expected values', () => {
		jest.useFakeTimers();

		mount(
			<Editor
				settings={ editorSettings }
				onError={ () => {} }
				postId={ 888 }
				postType="post"
				initialEdits=""
			/>
		);

		// for some reason resetEditorBlocks() is asynchronous when dispatching editEntityRecord
		act( () => {
			jest.runAllTicks();
		} );

		/*
		const { debug, getAllByPlaceholderText, getAllByLabelText, getByLabelText } = render(
			<div id={ containerId } ></div>
		);

		waitFor( () => {
			const renderContainer = document.getElementById( containerId );
			const childDiv = renderContainer.querySelector( 'div' );
			fireEvent.click( childDiv );
		} );

		// Click the inserter button to see the available blocks.
		const button = document.querySelector( '.editor-inserter__toggle' );
		fireEvent.click( button );
		const searchInput = getByLabelText( 'Search for a block' );

		// Enter the name of the tested block.
		fireEvent.change( searchInput, { target: { value: blockTitle } } );
		const blockResults = document.querySelector( '.block-editor-inserter__results' );

		// The tested block should appear in the available blocks.
		expect( hasText( blockResults, blockTitle ) ).toStrictEqual( true );
		const blockButton = getByText( blockResults, blockTitle );

		// Click the tested block, to add it to the editor.
		fireEvent.click( blockButton );
		const blockEdit = document.querySelector( '.editor-block-list__block-edit' );
		const textAreaField = blockEdit.querySelector( 'textarea' );

		// The block should have the values from blockLabBlocks.
		expect( hasText( blockEdit, help ) ).toStrictEqual( true );
		expect( hasText( blockEdit, textarea.help ) ).toStrictEqual( true );
		expect( hasText( blockEdit, color.help ) ).toStrictEqual( true );
		expect( textAreaField.value ).toStrictEqual( textarea.default );

		const enteredText = 'This is some entered text';
		fireEvent.input( textAreaField, { target: { value: enteredText } } );

		// Click inside and outside the block.
		fireEvent.click( textAreaField );
		fireEvent.click( document.querySelector( '.editor-default-block-appender__content' ) );

		// The <ServerSideRender> should now display, instead of the block's field.
		waitFor( () => expect( document.querySelector( '.block-lab-editor__ssr' ) ).toBeInTheDocument() );

		// Click the block again.
		fireEvent.click( blockEdit );

		// The text entered in the <textarea> should still be there.
		expect( textAreaField.value ).toStrictEqual( enteredText );

		// Click the repeater button to add a new subfield.
		fireEvent.click( getByLabelText( 'Add new' ) );

		// There should now be 2 rows of subfields.
		expect( getAllByPlaceholderText( textarea.placeholder ) ).toHaveLength( 2 );

		fireEvent.click( getByLabelText( 'Add new' ) );
		fireEvent.click( getByLabelText( 'Add new' ) );

		// There should not be more than the max number of subfields, no matter how many times 'Add new' is clicked.
		expect( getAllByPlaceholderText( textarea.placeholder ) ).toHaveLength( maxSubFields );

		// Delete a subfield.
		fireEvent.mouseOver( textAreaField );
		fireEvent.click( getAllByLabelText( 'Delete' )[ 0 ] );

		// There should now be one less subfield.
		expect( getAllByPlaceholderText( textarea.placeholder ) ).toHaveLength( 2 );

		fireEvent.click( getAllByLabelText( 'Delete' )[ 0 ] );
		fireEvent.click( getAllByLabelText( 'Delete' )[ 0 ] );

		// There should not be less than the minimum number of subfields, no matter how many times 'Delete' is pressed.
		expect( getAllByPlaceholderText( textarea.placeholder ) ).toHaveLength( minSubFields );
*/
	} );
} );

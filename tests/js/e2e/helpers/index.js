/**
 * Mainly taken from playground/index.js in Gutenberg.
 *
 * @see https://github.com/WordPress/gutenberg/blob/3dc8ebb8c933e7d1095863994b2a1f375c98c0ff/storybook/stories/playground/index.js
 */

/**
 * WordPress dependencies
 */
import {
	BlockEditorKeyboardShortcuts,
	BlockEditorProvider,
	BlockList,
	BlockInspector,
	WritingFlow,
	ObserveTyping,
} from '@wordpress/block-editor';
import { registerCoreBlocks } from '@wordpress/block-library';
import reducer from '@wordpress/blocks/src/store/reducer';
import * as selectors from '@wordpress/blocks/src/store/selectors';
import * as actions from '@wordpress/blocks/src/store/actions';
import {
	Popover,
	SlotFillProvider,
	DropZoneProvider,
} from '@wordpress/components';

import { registerStore } from '@wordpress/data';
import { useEffect, useState } from '@wordpress/element';

/**
 * Whether the node has the text in its textContent.
 *
 * @param {Object} nodeToSearch The element in which to search for the text.
 * @param {string} text The text to search the node for.
 */
export const hasText = ( nodeToSearch, text ) => -1 !== nodeToSearch.textContent.indexOf( text );

/**
 * Bootstraps the block editor.
 *
 * @param {Object} props The component props.
 * @param {Function} props.blockRegistration A function to register a tested block(s).
 */
export const BlockEditor = ( { blockRegistration } ) => {
	const [ blocks, updateBlocks ] = useState( [] );

	useEffect( () => {
		registerStore( 'core/blocks', { reducer, selectors, actions } );
		registerCoreBlocks();
		blockRegistration();
	}, [] );

	return (
		<div className="test-sandbox">
			<SlotFillProvider>
				<DropZoneProvider>
					<BlockEditorProvider
						value={ blocks }
						onInput={ updateBlocks }
						onChange={ updateBlocks }
					>
						<div className="test-sidebar">
							<BlockInspector />
						</div>
						<div className="editor-styles-wrapper">
							<Popover.Slot name="block-toolbar" />
							<BlockEditorKeyboardShortcuts />
							<WritingFlow>
								<ObserveTyping>
									<BlockList />
								</ObserveTyping>
							</WritingFlow>
						</div>
						<Popover.Slot />
					</BlockEditorProvider>
				</DropZoneProvider>
			</SlotFillProvider>
		</div>
	);
};

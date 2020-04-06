/**
 * Mainly taken from the playground/index.js in Gutenber.
 *
 * @see https://github.com/WordPress/gutenberg/blob/3dc8ebb8c933e7d1095863994b2a1f375c98c0ff/storybook/stories/playground/index.js
 */

/**
 * WordPress dependencies
 */
import { useEffect, useState } from '@wordpress/element';
import {
	BlockEditorKeyboardShortcuts,
	BlockEditorProvider,
	BlockList,
	BlockInspector,
	WritingFlow,
	ObserveTyping,
} from '@wordpress/block-editor';
import { registerCoreBlocks } from '@wordpress/block-library';
import {
	Popover,
	SlotFillProvider,
	DropZoneProvider,
} from '@wordpress/components';

const BlockEditor = ( { blockRegistration } ) => {
	const [ blocks, updateBlocks ] = useState( [] );

	useEffect( () => {
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

export default BlockEditor;

/**
 * WordPress dependencies
 */
const { dispatch } = wp.data;
const { blocks } = wp;

/**
 * Saves the Block Lab block configuration to the store.
 *
 * @param {Object} block The Block Lab block configuration to save.
 */
const saveBlock = ( block ) => {
	const jsonContent = JSON.stringify( [ block ] );
	dispatch( 'core/editor' ).editPost( { content: jsonContent } );
	dispatch( 'core/block-editor' ).resetBlocks( blocks.parse( jsonContent ) ); // Prevent the block editor from overwriting the saved content.
};

export default saveBlock;

// Mock wp object.

global.wp = {
	blocks: {
		registerBlockType: jest.fn(),
		parse: jest.fn(),
	},
	data: {
		dispatch: jest.fn( ( store ) => {
			if ( 'core/editor' === store ) {
				return {
					editPost: jest.fn(),
					getEditedPostContent: jest.fn(),
				};
			}

			if ( 'core/block-editor' === store ) {
				return { resetBlocks: jest.fn() };
			}
		} ),
		select: jest.fn( ( store ) => {
			if ( 'core/editor' === store ) {
				return { getEditedPostContent: jest.fn() }
			}
		} ),
	},
};

// Mock wp object.

global.wp = {
	blocks: {
		registerBlockType: jest.fn(),
	},
	data: {
		dispatch: ( store ) => {
			if ( 'core/editor' === store ) {
				return { editPost: jest.fn() };
			}
		},
		select: ( store ) => {
			if ( 'core/editor' === store ) {
				return { getEditedPostContent: jest.fn() };
			}
		},
	}
};

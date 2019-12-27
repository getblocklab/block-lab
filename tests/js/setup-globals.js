// Mock wp object.

global.wp = {
	blocks: { registerBlockType: jest.fn() },
};

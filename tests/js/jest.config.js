module.exports = {
	rootDir: '../../',
	...require( '@wordpress/scripts/config/jest-unit.config' ),
	setupFiles: [
		'<rootDir>/tests/js/setup-globals',
	],
	transform: {
		'^.+\\.[jt]sx?$': '<rootDir>/node_modules/@wordpress/scripts/config/babel-transform',
	},
	testPathIgnorePatterns: [
		'<rootDir>/.git',
		'<rootDir>/node_modules',
		'<rootDir>/js/build',
	],
	coveragePathIgnorePatterns: [
		'<rootDir>/node_modules',
		'<rootDir>/js/build',
	],
	coverageReporters: [ 'lcov' ],
	coverageDirectory: '<rootDir>/coverage',
	reporters: [ [ 'jest-silent-reporter', { useDots: true } ] ],
};

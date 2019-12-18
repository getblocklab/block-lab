const path = require( 'path' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const UglifyJSPlugin = require( 'uglifyjs-webpack-plugin' );

// Set different CSS extraction for editor only and common block styles
const blocksCSSPlugin = new MiniCssExtractPlugin( {
	filename: './css/blocks.style.css',
} );
const editBlocksCSSPlugin = new MiniCssExtractPlugin( {
	filename: './css/blocks.editor.css',
} );
const uglifyJSPlugin = new UglifyJSPlugin( {
	uglifyOptions: {
		mangle: {},
		compress: true,
	},
	sourceMap: false,
} );

// Configuration for the MiniCssExtractPlugin.
const extractConfig = {
	use: [
		{ loader: 'raw-loader' },
		{
			loader: 'postcss-loader',
			options: {
				plugins: [ require( 'autoprefixer' ) ],
			},
		},
		{
			loader: 'sass-loader',
		},
	],
};

module.exports = {
	entry: {
		'./js/editor.blocks': './js/blocks/index.js',
		'./js/scripts': './js/src/index.js',
	},
	output: {
		path: path.resolve( __dirname ),
		filename: '[name].js',
	},
	watch: false,
	// devtool: 'cheap-eval-source-map',
	mode: process.env.NODE_ENV === 'production' ? 'production' : 'development',
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /(node_modules|bower_components)/,
				use: {
					loader: 'babel-loader',
				},
			},
			{
				test: /style\.s?css$/,
				...extractConfig,
			},
			{
				test: /editor\.s?css$/,
				...extractConfig,
			},
		],
	},
	plugins: [
		blocksCSSPlugin,
		editBlocksCSSPlugin,
		uglifyJSPlugin,
	],
};

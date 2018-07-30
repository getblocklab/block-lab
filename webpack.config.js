const path = require( 'path' );
const ExtractTextPlugin = require( 'extract-text-webpack-plugin' );
const UglifyJSPlugin = require( 'uglifyjs-webpack-plugin' );

// Set different CSS extraction for editor only and common block styles
const blocksCSSPlugin = new ExtractTextPlugin( {
	filename: './css/blocks.style.css',
} );
const editBlocksCSSPlugin = new ExtractTextPlugin( {
	filename: './css/blocks.editor.css',
} );
const uglifyJSPlugin = new UglifyJSPlugin( {
	uglifyOptions: {
		mangle: {},
		compress: true
	},
	sourceMap: true
} );

// Configuration for the ExtractTextPlugin.
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
			query: {
				outputStyle: 'compressed',
			},
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
	devtool: 'cheap-eval-source-map',
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
				use: blocksCSSPlugin.extract( extractConfig ),
			},
			{
				test: /editor\.s?css$/,
				use: editBlocksCSSPlugin.extract( extractConfig ),
			},
		],
	},
	plugins: [
		blocksCSSPlugin,
		editBlocksCSSPlugin,
		uglifyJSPlugin,
	],
};

const path = require( 'path' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const UglifyJSPlugin = require( 'uglifyjs-webpack-plugin' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const isProduction = process.env.NODE_ENV === 'production';

module.exports = {
	...defaultConfig,
	entry: {
		'./js/editor.blocks': './js/blocks/index.js',
		'./js/scripts': './js/src/index.js',
	},
	output: {
		path: path.resolve( __dirname ),
		filename: '[name].js',
	},
	watch: false,
	mode: isProduction ? 'production' : 'development',
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
				test: /editor\.s?css$/,
				use: [
					{
						loader: MiniCssExtractPlugin.loader,
						options: {
							// Only allow hot module reloading in development.
							hmr: process.env.NODE_ENV === 'development',
							// Force reloading if hot module reloading does not work.
							reloadAll: true,
						},
					},
					'css-loader',
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
			},
		],
	},
	plugins: [
		new MiniCssExtractPlugin( {
			filename: './css/blocks.editor.css',
		} ),
		new UglifyJSPlugin( {
			uglifyOptions: {
				mangle: {},
				compress: true,
			},
			sourceMap: ! isProduction,
		} ),
	],
};

const path = require( 'path' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const DependencyExtractionWebpackPlugin = require( '@wordpress/dependency-extraction-webpack-plugin' );
const { defaultRequestToExternal, defaultRequestToHandle } = require( '@wordpress/dependency-extraction-webpack-plugin/util' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const isProduction = process.env.NODE_ENV === 'production';

module.exports = {
	...defaultConfig,
	entry: {
		'./js/editor.blocks': './js/blocks/index.js',
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
		new DependencyExtractionWebpackPlugin( {
			useDefaults: false,
			requestToHandle: ( request ) => {
				switch ( request ) {
					case '@wordpress/dom-ready':
					case '@wordpress/i18n':
					case '@wordpress/server-side-render':
					case '@wordpress/url':
						return undefined;

					default:
						return defaultRequestToHandle( request );
				}
			},
			requestToExternal: ( request ) => {
				switch ( request ) {
					case '@wordpress/dom-ready':
					case '@wordpress/i18n':
					case '@wordpress/server-side-render':
					case '@wordpress/url':
						return undefined;

					default:
						return defaultRequestToExternal( request );
				}
			},
		} ),
	],
};

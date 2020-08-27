const path = require( 'path' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const IgnoreEmitPlugin = require( 'ignore-emit-webpack-plugin' );

const DependencyExtractionWebpackPlugin = require( '@wordpress/dependency-extraction-webpack-plugin' );
const { defaultRequestToExternal, defaultRequestToHandle } = require( '@wordpress/dependency-extraction-webpack-plugin/lib/util' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const isProduction = process.env.NODE_ENV === 'production';

module.exports = {
	...defaultConfig,
	entry: {
		'./js/editor.blocks': './js/blocks/index.js',
		'./js/admin.migration': './js/migration/index.js',
		'./css/blocks.editor': './css/src/editor.scss',
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
		// Copied from Gutenberg.
		// MiniCSSExtractPlugin creates JavaScript assets for CSS that are
		// obsolete and should be removed. Related webpack issue:
		// https://github.com/webpack-contrib/mini-css-extract-plugin/issues/85
		new IgnoreEmitPlugin( [ 'blocks.editor.js' ] ),
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

const defaultConfig = require("@wordpress/scripts/config/webpack.config");
const postcssPresetEnv = require( 'postcss-preset-env' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const IgnoreEmitPlugin = require( 'ignore-emit-webpack-plugin' );
const production = process.env.NODE_ENV === '';
const path = require( 'path' );

module.exports = {
  ...defaultConfig,
  entry: {
    webSearch: path.resolve( process.cwd(), 'src/shortcodes/WebSearch', 'WebSearch.js' ),
    index: path.resolve( process.cwd(), 'src/js', 'index.ts' ),
	},
  devtool: 'source-map',
  resolve: {
    ...defaultConfig.module.resolve,
    extensions: [".ts", ".tsx", ".js", 'css','scss', 'sass']
	},
  module: {
	...defaultConfig.module,
	
    rules: [
      ...defaultConfig.module.rules,
      {
			test: /\.(sc|sa|c)ss$/,
			exclude: /node_modules/,
			use: [
				{
					loader: MiniCssExtractPlugin.loader,
				},
				{
					loader: 'css-loader',
					options: {
						sourceMap: ! production,
					},
				},
				{
					loader: 'postcss-loader',
					options: {
						ident: 'postcss',
						plugins: () => [
							postcssPresetEnv( {
								stage: 3,
								features: {
									'custom-media-queries': {
										preserve: false,
									},
									'custom-properties': {
										preserve: true,
									},
									'nesting-rules': true,
								},
							} ),
						],
					},
				},
				{
					loader: 'sass-loader',
					options: {
						sourceMap: ! production,
					},
				},
			],
		},
		{ test: /\.ts?$/, loader: "ts-loader" },
		{
			test: /\.(png|jpe?g|gif)$/i,
			use: [
			  {
				loader: 'file-loader',
			  },
			],
		  },
    ],
    
  },
  performance: {
    hints: false,
    maxEntrypointSize: 512000,
    maxAssetSize: 512000
  },
  plugins: [
    ...defaultConfig.plugins,
    new MiniCssExtractPlugin( {
      filename: '[name].css',
    } ),
   // new IgnoreEmitPlugin( [ 'editor.js', 'style.js' ] ),
  ],
};
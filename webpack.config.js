const path = require("path");
const webpack = require("webpack");
const glob = require("glob");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const RemoveEmptyScriptsPlugin = require("webpack-remove-empty-scripts");
const FixStyleOnlyEntriesPlugin = require("webpack-fix-style-only-entries");
const WebpackWatchedGlobEntries = require("webpack-watched-glob-entries-plugin");
const sass = require("sass");
const cssnano = require("cssnano");
const autoprefixer = require("autoprefixer");
const TerserPlugin = require("terser-webpack-plugin");

const DIST_DIR = path.resolve(__dirname, "./assets");

const entries = WebpackWatchedGlobEntries.getEntries(
  [
    path.resolve(__dirname, "./src/scss/*.scss"),
    path.resolve(__dirname, "./src/js/*.js"),
    path.resolve(__dirname, "./src/vendor/*.js"),
  ],
  {
    ignore: path.resolve(__dirname, "./src/scss/_*.scss"),
    ignore: path.resolve(__dirname, "./src/js/_*.js"),
  }
)();

module.exports = {
  target: ["web", "es5"],
  cache: {
    type: "filesystem",
    buildDependencies: {
      config: [__filename],
    },
  },
  entry: entries,
  output: {
    path: DIST_DIR,
    filename: "./js/[name].js",
  },
  optimization: {
    splitChunks: {
      chunks: "all",
      minSize: 0,
      cacheGroups: {
        vendor: {
          name: "../vendor/vendor",
          test: /[\\/]node_modules[\\/]/,
          priority: -10,
        },
        default: false,
      },
    },
    minimizer: [
      new TerserPlugin({
        extractComments: false,
      }),
    ],
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        use: 'babel-loader'
      },
      {
        test: [/.css$|.scss$/],
        use: [
          MiniCssExtractPlugin.loader,
          {
            loader: "css-loader",
            options: {
              // url() を require() に変換しない
              url: false,
              // css-loader の前に loader を 2つ (postcss, sass) 実行する
              importLoaders: 2,
            },
          },
          {
            loader: "postcss-loader",
            options: {
              postcssOptions: {
                plugins: [
                  [
                    cssnano,
                    {
                      // コメントを削除する
                      preset: [
                        "default",
                        { discardComments: { removeAll: true } },
                      ],
                    },
                  ],
                  [autoprefixer, { grid: true }],
                ],
              },
            },
          },
          {
            loader: "sass-loader",
            options: {
              // dart-sass を使用する
              implementation: sass,
            },
          },
        ],
      },
      {
        test: require.resolve("jquery"),
        loader: "expose-loader",
        options: {
          exposes: ["$", "jQuery"],
        },
      },
    ],
  },
  plugins: [
    new RemoveEmptyScriptsPlugin(),
    new MiniCssExtractPlugin({
      filename: "./css/[name].css",
    }),

  ],
};

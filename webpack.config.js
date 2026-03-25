const path = require('path');

function isFileExists(path) {
  const fs = require('fs');
  try {
    fs.statSync(path);
    return true;
  } catch (err) {
    if (err.code === 'ENOENT') {
      return false;
    } else {
      throw err;
    }
  }
};

const mode = isFileExists(path.resolve(__dirname, '.production')) ? 'production' : 'development';
const forceAnalyze = false;

module.exports = {
  mode: mode,
  entry: {
    'counter-app.js': './resources/react/counter-app',
    'index-app.js': './resources/react/index-app',
  },
  output: {
    path: path.resolve(__dirname, 'resources', '.compiled', 'react'),
    filename: '[name]',
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        use: [
          {
            loader: 'babel-loader',
            options: {
              presets: [
                ['@babel/env', {
                  useBuiltIns: 'entry',
                  corejs: 3,
                }],
                ['@babel/react', { runtime: 'automatic' }],
              ],
            },
          },
        ],
      },
      {
        test: /\.module\.css$/,
        use: [
          {
            loader: 'style-loader',
            options: {
              esModule: false,
            },
          },
          {
            loader: 'css-loader',
            options: {
              esModule: false,
              modules: {
                namedExport: false,
                exportLocalsConvention: 'as-is',
                localIdentName: mode === 'production'
                  ? '[hash:base64:8]'
                  : '[name]__[local]--[hash:base64:5]',
              },
            },
          },
        ],
      },
    ],
  },
  plugins: [],
};

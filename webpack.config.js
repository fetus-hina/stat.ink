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
    'counter-app.js': './resources/react/counter-app.tsx',
    'index-app.js': './resources/react/index-app.tsx',
  },
  output: {
    path: path.resolve(__dirname, 'resources', '.compiled', 'react'),
    filename: '[name]',
  },
  resolve: {
    extensions: ['.tsx', '.ts', '.js'],
  },
  module: {
    rules: [
      {
        test: /\.tsx?$/,
        use: {
          loader: 'ts-loader',
          options: {
            configFile: path.resolve(__dirname, 'tsconfig.react.json'),
          },
        },
        exclude: /node_modules/,
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

import 'core-js/stable';
import 'regenerator-runtime/runtime';
import IndexApp from './components/IndexApp';
import React from 'react';
import ReactDOM from 'react-dom';
import store from './store/indexApp';
import { Provider } from 'react-redux';

ReactDOM.render(
  <Provider store={store}>
    <IndexApp />
  </Provider>,
  document.getElementById('index-app')
);

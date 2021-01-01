/**
 * @license
 *
 * stat.ink
 *
 * Copyright (C) 2015-2021 AIZAWA Hina, All rights reserved.
 * Licended under the MIT License
 */

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

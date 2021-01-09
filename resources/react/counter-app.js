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
import CounterApp from './components/CounterApp';
import React from 'react';
import ReactDOM from 'react-dom';
import store from './store/counterApp';
import { Provider } from 'react-redux';

ReactDOM.render(
  <Provider store={store}>
    <CounterApp />
  </Provider>,
  document.getElementById('counter-app')
);

/**
 * @license
 *
 * stat.ink
 *
 * Copyright (C) 2015-2023 AIZAWA Hina, All rights reserved.
 * Licended under the MIT License
 */

import 'core-js/stable';
import 'regenerator-runtime/runtime';
import IndexApp from './components/IndexApp';
import React from 'react';
import store from './store/indexApp';
import { Provider } from 'react-redux';
import { createRoot } from 'react-dom/client';

createRoot(document.getElementById('index-app')).render(
  <Provider store={store}>
    <IndexApp />
  </Provider>
);

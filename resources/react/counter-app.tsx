/**
 * @license
 *
 * stat.ink
 *
 * Copyright (C) 2015-2023 AIZAWA Hina, All rights reserved.
 * Licended under the MIT License
 */

import CounterApp from './components/CounterApp';
import store from './store/counterApp';
import { Provider } from 'react-redux';
import { createRoot } from 'react-dom/client';

createRoot(document.getElementById('counter-app')!).render(
  <Provider store={store}>
    <CounterApp />
  </Provider>
);

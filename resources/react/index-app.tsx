/**
 * @license
 *
 * stat.ink
 *
 * Copyright (C) 2015-2023 AIZAWA Hina, All rights reserved.
 * Licended under the MIT License
 */

import IndexApp from './components/IndexApp';
import store from './store/indexApp';
import { Provider } from 'react-redux';
import { bootstrapLatestBattles } from './actions/latestBattles';
import { bootstrapMyLatestBattles } from './actions/myLatestBattles';
import { createRoot } from 'react-dom/client';

const container = document.getElementById('index-app')!;
const loggedIn = container.dataset.loggedIn === '1';

const latestHeading = container.dataset.latestHeading;
if (latestHeading) {
  store.dispatch(bootstrapLatestBattles({ heading: latestHeading }));
}

if (loggedIn) {
  const myLatestHeading = container.dataset.myLatestHeading;
  const userName = container.dataset.userName;
  const userUrl = container.dataset.userUrl;
  if (myLatestHeading && userName !== undefined && userUrl !== undefined) {
    store.dispatch(bootstrapMyLatestBattles({
      heading: myLatestHeading,
      user: { name: userName, url: userUrl }
    }));
  }
}

createRoot(container).render(
  <Provider store={store}>
    <IndexApp loggedIn={loggedIn} />
  </Provider>
);

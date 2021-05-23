import blogSaga from '../saga/blog';
import createSagaMiddleware from 'redux-saga';
import latestBattlesSaga from '../saga/latestBattles';
import myLatestBattlesSaga from '../saga/myLatestBattles';
import reducer from '../reducers/indexApp';
import scheduleSaga from '../saga/schedule';
import { all } from 'redux-saga/effects';
import { applyMiddleware, createStore } from 'redux';

function * rootSaga () {
  yield all([
    ...blogSaga,
    ...latestBattlesSaga,
    ...myLatestBattlesSaga,
    ...scheduleSaga
  ]);
}

const sagaMiddleware = createSagaMiddleware();

const configureStore = () => {
  const store = createStore(reducer, applyMiddleware(sagaMiddleware));
  sagaMiddleware.run(rootSaga);
  return store;
};

const store = configureStore();

if (process.env.NODE_ENV === 'development') {
  store.subscribe(() => {
    const state = store.getState();
    console.log(state);
  });
}

export default store;

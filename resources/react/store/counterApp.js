import counterSaga from '../saga/counter';
import createSagaMiddleware from 'redux-saga';
import reducer from '../reducers/counterApp';
import { all } from 'redux-saga/effects';
import { applyMiddleware, createStore } from 'redux';

function* rootSaga() {
  yield all([
    ...counterSaga,
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

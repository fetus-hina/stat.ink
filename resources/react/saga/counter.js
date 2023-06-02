import axios from 'axios';

import {
  call,
  put,
  takeLatest
} from 'redux-saga/effects';

import {
  FETCH_COUNTER,
  fetchCounterFailed,
  fetchCounterSuccess
} from '../actions/counter';

function requestGetApi () {
  return axios
    .get('/api/internal/counter')
    .then(response => {
      return {
        data: response.data
      };
    })
    .catch(error => {
      return {
        error
      };
    });
}

function * fetch () {
  const { data, error } = yield call(requestGetApi);

  if (data) {
    yield put(fetchCounterSuccess(data));
  } else {
    yield put(fetchCounterFailed(error));
  }
}

export default [takeLatest(FETCH_COUNTER, fetch)];

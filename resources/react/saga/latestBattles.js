import axios from 'axios';

import {
  call,
  put,
  takeLatest
} from 'redux-saga/effects';

import {
  FETCH_LATEST_BATTLES,
  fetchLatestBattlesFailed,
  fetchLatestBattlesSuccess
} from '../actions/latestBattles';

function requestGetApi () {
  return axios
    .get('/api/internal/latest-battles')
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
    yield put(fetchLatestBattlesSuccess(data));
  } else {
    yield put(fetchLatestBattlesFailed(error));
  }
}

export default [takeLatest(FETCH_LATEST_BATTLES, fetch)];

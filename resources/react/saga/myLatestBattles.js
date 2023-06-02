import axios from 'axios';

import {
  call,
  put,
  takeLatest
} from 'redux-saga/effects';

import {
  FETCH_MY_LATEST_BATTLES,
  fetchMyLatestBattlesFailed,
  fetchMyLatestBattlesSuccess
} from '../actions/myLatestBattles';

function requestGetApi () {
  return axios
    .get('/api/internal/my-latest-battles')
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
    yield put(fetchMyLatestBattlesSuccess(data));
  } else {
    yield put(fetchMyLatestBattlesFailed(error));
  }
}

export default [takeLatest(FETCH_MY_LATEST_BATTLES, fetch)];

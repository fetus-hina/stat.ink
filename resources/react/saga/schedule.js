import axios from 'axios';

import {
  call,
  put,
  takeLatest
} from 'redux-saga/effects';

import {
  FETCH_SCHEDULE,
  fetchScheduleFailed,
  fetchScheduleSuccess
} from '../actions/schedule';

function requestGetApi () {
  return axios
    .get('/api/internal/schedule')
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
    yield put(fetchScheduleSuccess(data));
  } else {
    yield put(fetchScheduleFailed(error));
  }
}

export default [takeLatest(FETCH_SCHEDULE, fetch)];

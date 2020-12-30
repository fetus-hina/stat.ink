import axios from 'axios';

import {
  call,
  put,
  takeEvery,
  takeLatest,
} from 'redux-saga/effects';

import {
  FETCH_SCHEDULE,
  fetchSchedule,
  fetchScheduleFailed,
  fetchScheduleSuccess,
} from '../actions/schedule';

function requestGetApi() {
  return axios
    .get('/api/internal/schedule')
    .then(response => {
      return {
        data: response.data,
      };
    })
    .catch(error => {
      return {
        error: error,
      };
    });
}

function* fetch() {
  const { data, error } = yield call(requestGetApi);

  if (data) {
    yield put(fetchScheduleSuccess(data));
  } else {
    yield put(fetchScheduleFailed(data));
  }
}

export default [takeLatest(FETCH_SCHEDULE, fetch)];

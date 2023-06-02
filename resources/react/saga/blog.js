import axios from 'axios';

import {
  call,
  put,
  takeLatest
} from 'redux-saga/effects';

import {
  FETCH_BLOG_ENTRY,
  fetchBlogEntryFailed,
  fetchBlogEntrySuccess
} from '../actions/blog';

function requestGetApi () {
  return axios
    .get('/api/internal/blog-entry')
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
    yield put(fetchBlogEntrySuccess(data));
  } else {
    yield put(fetchBlogEntryFailed(error));
  }
}

export default [takeLatest(FETCH_BLOG_ENTRY, fetch)];

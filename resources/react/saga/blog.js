import axios from 'axios';

import {
  call,
  put,
  takeEvery,
  takeLatest,
} from 'redux-saga/effects';

import {
  FETCH_BLOG_ENTRY,
  fetchBlogEntry,
  fetchBlogEntryFailed,
  fetchBlogEntrySuccess,
} from '../actions/blog';

function requestGetApi() {
  return axios
    .get('/api/internal/blog-entry')
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
    yield put(fetchBlogEntrySuccess(data));
  } else {
    yield put(fetchBlogEntryFailed(data));
  }
}

export default [takeLatest(FETCH_BLOG_ENTRY, fetch)];

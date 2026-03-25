import {
  FETCH_BLOG_ENTRY,
  FETCH_BLOG_ENTRY_SUCCESS,
  FETCH_BLOG_ENTRY_FAILED
} from '../../actions/blog';

import {
  BLOG_ENTRIES_LIFETIME,
  EXPIRED_TIMESTAMP,
  STATUS_EXPIRED,
  STATUS_FAILED,
  STATUS_LOADING,
  STATUS_OK
} from '../../constants';

import { BlogEntry } from '../../types';

const initialState = {
  data: [] as BlogEntry[],
  expires: EXPIRED_TIMESTAMP,
  status: STATUS_EXPIRED as string
};

function reduceFetch (oldState: typeof initialState) {
  const state = Object.assign({}, oldState); // copy
  state.status = STATUS_LOADING;
  state.expires = (new Date()).getTime() + (10 * 365 * 86400 * 1000);
  return state;
}

function reduceFetchFailed (oldState: typeof initialState) {
  const state = Object.assign({}, oldState); // copy
  state.status = STATUS_FAILED;
  state.expires = (new Date()).getTime() + BLOG_ENTRIES_LIFETIME;
  return state;
}

function reduceFetchSuccess (oldState: typeof initialState, action: { type: string; value?: BlogEntry[] }) {
  const state = Object.assign({}, oldState); // copy
  state.status = STATUS_OK;
  state.data = action.value!;
  state.expires = (new Date()).getTime() + BLOG_ENTRIES_LIFETIME;
  return state;
}

export default function reduce (state = initialState, action: { type: string; value?: BlogEntry[] } = { type: '' }) {
  switch (action.type) {
    case FETCH_BLOG_ENTRY: return reduceFetch(state);
    case FETCH_BLOG_ENTRY_FAILED: return reduceFetchFailed(state);
    case FETCH_BLOG_ENTRY_SUCCESS: return reduceFetchSuccess(state, action);
    default: return state;
  }
}

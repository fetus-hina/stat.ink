import {
  FETCH_COUNTER,
  FETCH_COUNTER_SUCCESS,
  FETCH_COUNTER_FAILED
} from '../../actions/counter';

import {
  COUNTER_LIFETIME,
  EXPIRED_TIMESTAMP,
  STATUS_EXPIRED,
  STATUS_FAILED,
  STATUS_LOADING,
  STATUS_OK
} from '../../constants';

interface Action {
  type: string;
  value?: any;
}

const initialState = {
  data: {} as any,
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
  state.expires = (new Date()).getTime() + COUNTER_LIFETIME;
  return state;
}

function reduceFetchSuccess (oldState: typeof initialState, action: Action) {
  const state = Object.assign({}, oldState); // copy
  state.status = STATUS_OK;
  state.data = action.value;
  state.expires = (new Date()).getTime() + COUNTER_LIFETIME;
  return state;
}

export default function reduce (state = initialState, action: Action = { type: '' }) {
  switch (action.type) {
    case FETCH_COUNTER: return reduceFetch(state);
    case FETCH_COUNTER_FAILED: return reduceFetchFailed(state);
    case FETCH_COUNTER_SUCCESS: return reduceFetchSuccess(state, action);
    default: return state;
  }
}

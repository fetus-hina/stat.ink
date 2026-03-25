import {
  FETCH_LATEST_BATTLES,
  FETCH_LATEST_BATTLES_SUCCESS,
  FETCH_LATEST_BATTLES_FAILED
} from '../../actions/latestBattles';

import {
  EXPIRED_TIMESTAMP,
  LATEST_BATTLES_LIFETIME,
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
  data: [] as any[],
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
  state.data = [];
  state.expires = (new Date()).getTime() + LATEST_BATTLES_LIFETIME;
  state.status = STATUS_FAILED;
  return state;
}

function reduceFetchSuccess (oldState: typeof initialState, action: Action) {
  const state = Object.assign({}, oldState); // copy
  state.data = action.value;
  state.expires = (new Date()).getTime() + LATEST_BATTLES_LIFETIME;
  state.status = STATUS_OK;
  return state;
}

export default function reduce (state = initialState, action: Action = { type: '' }) {
  switch (action.type) {
    case FETCH_LATEST_BATTLES: return reduceFetch(state);
    case FETCH_LATEST_BATTLES_FAILED: return reduceFetchFailed(state);
    case FETCH_LATEST_BATTLES_SUCCESS: return reduceFetchSuccess(state, action);
    default: return state;
  }
}

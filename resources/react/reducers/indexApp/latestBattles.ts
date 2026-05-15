import {
  BOOTSTRAP_LATEST_BATTLES,
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

import { LatestBattlesBootstrap, LatestBattlesData } from '../../types';

const initialState = {
  data: null as LatestBattlesData | null,
  bootstrap: null as LatestBattlesBootstrap | null,
  expires: EXPIRED_TIMESTAMP,
  status: STATUS_EXPIRED as string
};

type Action =
  | { type: typeof BOOTSTRAP_LATEST_BATTLES; value: LatestBattlesBootstrap }
  | { type: typeof FETCH_LATEST_BATTLES }
  | { type: typeof FETCH_LATEST_BATTLES_FAILED; value?: unknown }
  | { type: typeof FETCH_LATEST_BATTLES_SUCCESS; value: LatestBattlesData }
  | { type: '' };

function reduceBootstrap (oldState: typeof initialState, action: { value: LatestBattlesBootstrap }) {
  const state = Object.assign({}, oldState);
  state.bootstrap = action.value;
  return state;
}

function reduceFetch (oldState: typeof initialState) {
  const state = Object.assign({}, oldState); // copy
  state.status = STATUS_LOADING;
  state.expires = (new Date()).getTime() + (10 * 365 * 86400 * 1000);
  return state;
}

function reduceFetchFailed (oldState: typeof initialState) {
  const state = Object.assign({}, oldState); // copy
  state.data = null;
  state.expires = (new Date()).getTime() + LATEST_BATTLES_LIFETIME;
  state.status = STATUS_FAILED;
  return state;
}

function reduceFetchSuccess (oldState: typeof initialState, action: { value: LatestBattlesData }) {
  const state = Object.assign({}, oldState); // copy
  state.data = action.value;
  state.expires = (new Date()).getTime() + LATEST_BATTLES_LIFETIME;
  state.status = STATUS_OK;
  return state;
}

export default function reduce (state = initialState, action: Action = { type: '' }) {
  switch (action.type) {
    case BOOTSTRAP_LATEST_BATTLES: return reduceBootstrap(state, action);
    case FETCH_LATEST_BATTLES: return reduceFetch(state);
    case FETCH_LATEST_BATTLES_FAILED: return reduceFetchFailed(state);
    case FETCH_LATEST_BATTLES_SUCCESS: return reduceFetchSuccess(state, action);
    default: return state;
  }
}

import {
  FETCH_SCHEDULE,
  FETCH_SCHEDULE_FAILED,
  FETCH_SCHEDULE_SUCCESS,
  SCHEDULE_TICK_TIME
} from '../../actions/schedule';

import {
  SCHEDULE_MAX_LIFETIME,
  EXPIRED_TIMESTAMP,
  STATUS_EXPIRED,
  STATUS_FAILED,
  STATUS_LOADING,
  STATUS_OK
} from '../../constants';

import { ScheduleData } from '../../types';

const initialState = {
  currentTime: (new Date()).getTime(),
  data: null as ScheduleData | null,
  expires: EXPIRED_TIMESTAMP,
  status: STATUS_EXPIRED as string
};

function reduceFetch (oldState: typeof initialState) {
  const state = Object.assign({}, oldState); // copy
  state.status = STATUS_LOADING;
  state.expires = (new Date()).getTime() + SCHEDULE_MAX_LIFETIME;
  return state;
}

function reduceFetchFailed (oldState: typeof initialState) {
  const state = Object.assign({}, oldState); // copy
  state.status = STATUS_FAILED;
  state.expires = (new Date()).getTime() + (1 * 60 * 1000);
  return state;
}

function reduceFetchSuccess (oldState: typeof initialState, action: { type: string; value?: ScheduleData }) {
  const state = Object.assign({}, oldState); // copy
  state.currentTime = action.value!.time * 1000;
  state.data = action.value!;
  state.expires = (new Date()).getTime() + SCHEDULE_MAX_LIFETIME;
  state.status = STATUS_OK;
  return state;
}

function reduceTick (oldState: typeof initialState) {
  const state = Object.assign({}, oldState); // copy
  state.currentTime += 1000;
  return state;
}

export default function reduce (state = initialState, action: { type: string; value?: ScheduleData } = { type: '' }) {
  switch (action.type) {
    case FETCH_SCHEDULE: return reduceFetch(state);
    case FETCH_SCHEDULE_FAILED: return reduceFetchFailed(state);
    case FETCH_SCHEDULE_SUCCESS: return reduceFetchSuccess(state, action);
    case SCHEDULE_TICK_TIME: return reduceTick(state);
    default: return state;
  }
}

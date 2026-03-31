import { Dispatch } from '@reduxjs/toolkit';
import { ScheduleData } from '../types';

export const FETCH_SCHEDULE = 'FETCH_SCHEDULE';
export const FETCH_SCHEDULE_FAILED = 'FETCH_SCHEDULE_FAILED';
export const FETCH_SCHEDULE_SUCCESS = 'FETCH_SCHEDULE_SUCCESS';
export const SCHEDULE_TICK_TIME = 'SCHEDULE_TICK_TIME';

export function fetchScheduleFailed (error: unknown) {
  return {
    type: FETCH_SCHEDULE_FAILED,
    value: error
  };
}

export function fetchScheduleSuccess (data: ScheduleData) {
  return {
    type: FETCH_SCHEDULE_SUCCESS,
    value: data
  };
}

export function scheduleTickTime () {
  return {
    type: SCHEDULE_TICK_TIME
  };
}

export function fetchSchedule () {
  return (dispatch: Dispatch) => {
    dispatch({ type: FETCH_SCHEDULE });
    return fetch('/api/internal/schedule')
      .then(response => response.json())
      .then(data => {
        dispatch(fetchScheduleSuccess(data));
      })
      .catch(error => {
        dispatch(fetchScheduleFailed(error));
      });
  };
}

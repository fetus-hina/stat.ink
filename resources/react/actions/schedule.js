export const FETCH_SCHEDULE = 'FETCH_SCHEDULE';
export const FETCH_SCHEDULE_FAILED = 'FETCH_SCHEDULE_FAILED';
export const FETCH_SCHEDULE_SUCCESS = 'FETCH_SCHEDULE_SUCCESS';
export const SCHEDULE_TICK_TIME = 'SCHEDULE_TICK_TIME';

export function fetchSchedule () {
  return {
    type: FETCH_SCHEDULE
  };
}

export function fetchScheduleFailed (error) {
  return {
    type: FETCH_SCHEDULE_FAILED,
    value: error
  };
}

export function fetchScheduleSuccess (data) {
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

import { Dispatch } from '@reduxjs/toolkit';
import { CounterData } from '../types';

export const FETCH_COUNTER = 'FETCH_COUNTER';
export const FETCH_COUNTER_FAILED = 'FETCH_COUNTER_FAILED';
export const FETCH_COUNTER_SUCCESS = 'FETCH_COUNTER_SUCCESS';

export function fetchCounterFailed (error: unknown) {
  return {
    type: FETCH_COUNTER_FAILED,
    value: error
  };
}

export function fetchCounterSuccess (data: CounterData) {
  return {
    type: FETCH_COUNTER_SUCCESS,
    value: data
  };
}

export function fetchCounter () {
  return (dispatch: Dispatch) => {
    dispatch({ type: FETCH_COUNTER });
    return fetch('/api/internal/counter')
      .then(response => response.json())
      .then(data => {
        dispatch(fetchCounterSuccess(data));
      })
      .catch(error => {
        dispatch(fetchCounterFailed(error));
      });
  };
}

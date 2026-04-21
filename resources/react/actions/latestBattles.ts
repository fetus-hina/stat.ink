import { Dispatch } from '@reduxjs/toolkit';
import { LatestBattlesData } from '../types';

export const FETCH_LATEST_BATTLES = 'FETCH_LATEST_BATTLES';
export const FETCH_LATEST_BATTLES_FAILED = 'FETCH_LATEST_BATTLES_FAILED';
export const FETCH_LATEST_BATTLES_SUCCESS = 'FETCH_LATEST_BATTLES_SUCCESS';

export function fetchLatestBattlesFailed (error: unknown) {
  return {
    type: FETCH_LATEST_BATTLES_FAILED,
    value: error
  };
}

export function fetchLatestBattlesSuccess (data: LatestBattlesData) {
  return {
    type: FETCH_LATEST_BATTLES_SUCCESS,
    value: data
  };
}

export function fetchLatestBattles () {
  return (dispatch: Dispatch) => {
    dispatch({ type: FETCH_LATEST_BATTLES });
    return fetch('/api/internal/latest-battles')
      .then(response => response.json())
      .then(data => {
        dispatch(fetchLatestBattlesSuccess(data));
      })
      .catch(error => {
        dispatch(fetchLatestBattlesFailed(error));
      });
  };
}

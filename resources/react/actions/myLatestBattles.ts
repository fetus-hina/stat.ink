import { Dispatch } from '@reduxjs/toolkit';
import { LatestBattlesData } from '../types';

export const FETCH_MY_LATEST_BATTLES = 'FETCH_MY_LATEST_BATTLES';
export const FETCH_MY_LATEST_BATTLES_FAILED = 'FETCH_MY_LATEST_BATTLES_FAILED';
export const FETCH_MY_LATEST_BATTLES_SUCCESS = 'FETCH_MY_LATEST_BATTLES_SUCCESS';

export function fetchMyLatestBattlesFailed (error: unknown) {
  return {
    type: FETCH_MY_LATEST_BATTLES_FAILED,
    value: error
  };
}

export function fetchMyLatestBattlesSuccess (data: LatestBattlesData) {
  return {
    type: FETCH_MY_LATEST_BATTLES_SUCCESS,
    value: data
  };
}

export function fetchMyLatestBattles () {
  return (dispatch: Dispatch) => {
    dispatch({ type: FETCH_MY_LATEST_BATTLES });
    return fetch('/api/internal/my-latest-battles')
      .then(response => response.json())
      .then(data => {
        dispatch(fetchMyLatestBattlesSuccess(data));
      })
      .catch(error => {
        dispatch(fetchMyLatestBattlesFailed(error));
      });
  };
}

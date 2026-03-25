import { Dispatch } from '@reduxjs/toolkit';
import axios from 'axios';
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
    return axios
      .get('/api/internal/latest-battles')
      .then(response => {
        dispatch(fetchLatestBattlesSuccess(response.data));
      })
      .catch(error => {
        dispatch(fetchLatestBattlesFailed(error));
      });
  };
}

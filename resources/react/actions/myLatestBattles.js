import axios from 'axios';

export const FETCH_MY_LATEST_BATTLES = 'FETCH_MY_LATEST_BATTLES';
export const FETCH_MY_LATEST_BATTLES_FAILED = 'FETCH_MY_LATEST_BATTLES_FAILED';
export const FETCH_MY_LATEST_BATTLES_SUCCESS = 'FETCH_MY_LATEST_BATTLES_SUCCESS';

export function fetchMyLatestBattlesFailed (error) {
  return {
    type: FETCH_MY_LATEST_BATTLES_FAILED,
    value: error
  };
}

export function fetchMyLatestBattlesSuccess (data) {
  return {
    type: FETCH_MY_LATEST_BATTLES_SUCCESS,
    value: data
  };
}

export function fetchMyLatestBattles () {
  return (dispatch) => {
    dispatch({ type: FETCH_MY_LATEST_BATTLES });
    return axios
      .get('/api/internal/my-latest-battles')
      .then(response => {
        dispatch(fetchMyLatestBattlesSuccess(response.data));
      })
      .catch(error => {
        dispatch(fetchMyLatestBattlesFailed(error));
      });
  };
}

import axios from 'axios';

export const FETCH_COUNTER = 'FETCH_COUNTER';
export const FETCH_COUNTER_FAILED = 'FETCH_COUNTER_FAILED';
export const FETCH_COUNTER_SUCCESS = 'FETCH_COUNTER_SUCCESS';

export function fetchCounterFailed (error) {
  return {
    type: FETCH_COUNTER_FAILED,
    value: error
  };
}

export function fetchCounterSuccess (data) {
  return {
    type: FETCH_COUNTER_SUCCESS,
    value: data
  };
}

export function fetchCounter () {
  return (dispatch) => {
    dispatch({ type: FETCH_COUNTER });
    return axios
      .get('/api/internal/counter')
      .then(response => {
        dispatch(fetchCounterSuccess(response.data));
      })
      .catch(error => {
        dispatch(fetchCounterFailed(error));
      });
  };
}

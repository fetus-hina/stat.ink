export const FETCH_MY_LATEST_BATTLES = 'FETCH_MY_LATEST_BATTLES';
export const FETCH_MY_LATEST_BATTLES_FAILED = 'FETCH_MY_LATEST_BATTLES_FAILED';
export const FETCH_MY_LATEST_BATTLES_SUCCESS = 'FETCH_MY_LATEST_BATTLES_SUCCESS';

export function fetchMyLatestBattles () {
  return {
    type: FETCH_MY_LATEST_BATTLES
  };
}

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

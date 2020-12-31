export const FETCH_LATEST_BATTLES = 'FETCH_LATEST_BATTLES';
export const FETCH_LATEST_BATTLES_FAILED = 'FETCH_LATEST_BATTLES_FAILED';
export const FETCH_LATEST_BATTLES_SUCCESS = 'FETCH_LATEST_BATTLES_SUCCESS';

export function fetchLatestBattles() {
  return {
    type: FETCH_LATEST_BATTLES,
  };
}

export function fetchLatestBattlesFailed(error) {
  return {
    type: FETCH_LATEST_BATTLES_FAILED,
    value: error,
  };
}

export function fetchLatestBattlesSuccess(data) {
  return {
    type: FETCH_LATEST_BATTLES_SUCCESS,
    value: data,
  };
}

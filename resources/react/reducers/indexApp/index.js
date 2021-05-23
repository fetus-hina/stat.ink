import blog from './blog';
import latestBattles from './latestBattles';
import myLatestBattles from './myLatestBattles';
import schedule from './schedule';
import { combineReducers } from 'redux';

const reducers = combineReducers({
  blog,
  latestBattles,
  myLatestBattles,
  schedule
});

export default reducers;

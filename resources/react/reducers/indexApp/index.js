import blog from './blog';
import schedule from './schedule';
import { combineReducers } from 'redux';

const reducers = combineReducers({
  blog,
  schedule,
});

export default reducers;

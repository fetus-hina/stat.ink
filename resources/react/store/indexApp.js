import reducer from '../reducers/indexApp';
import { configureStore } from '@reduxjs/toolkit';

const store = configureStore({
  reducer,
  devTools: process.env.NODE_ENV === 'development'
});

export default store;

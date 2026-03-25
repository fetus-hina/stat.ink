import reducer from '../reducers/counterApp';
import { configureStore } from '@reduxjs/toolkit';

const store = configureStore({
  reducer,
  devTools: process.env.NODE_ENV === 'development'
});

export default store;

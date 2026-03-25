import reducer from '../reducers/indexApp';
import { configureStore } from '@reduxjs/toolkit';

export const store = configureStore({
  reducer,
  devTools: process.env.NODE_ENV === 'development'
});

export type IndexRootState = ReturnType<typeof store.getState>;
export type IndexAppDispatch = typeof store.dispatch;

export default store;

import reducer from '../reducers/counterApp';
import { configureStore } from '@reduxjs/toolkit';

export const store = configureStore({
  reducer,
  devTools: process.env.NODE_ENV === 'development'
});

export type CounterRootState = ReturnType<typeof store.getState>;
export type CounterAppDispatch = typeof store.dispatch;

export default store;

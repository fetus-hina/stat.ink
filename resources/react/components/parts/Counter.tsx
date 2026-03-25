import Impl from './counter/Counter';
import { useEffect, useRef } from 'react';
import { useSelector, useDispatch } from 'react-redux';
import { fetchCounter } from '../../actions/counter';
import type { CounterRootState, CounterAppDispatch } from '../../store/counterApp';

export default function Counter () {
  const dispatch = useDispatch<CounterAppDispatch>();
  const expires = useSelector((state: CounterRootState) => state.counter.expires);
  const expiresRef = useRef(expires);
  expiresRef.current = expires;

  useEffect(() => {
    dispatch(fetchCounter());
    const timer = window.setInterval(() => {
      if (expiresRef.current > (new Date()).getTime()) {
        return;
      }
      dispatch(fetchCounter());
    }, 500);
    return () => { window.clearInterval(timer); };
  }, [dispatch]);

  return <Impl />;
}

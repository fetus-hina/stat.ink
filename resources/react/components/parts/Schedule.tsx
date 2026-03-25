import Impl from './schedule/Schedule';
import { useEffect, useRef } from 'react';
import { useSelector, useDispatch } from 'react-redux';
import { fetchSchedule, scheduleTickTime } from '../../actions/schedule';

export default function Schedule () {
  const dispatch: any = useDispatch();
  const expires = useSelector((state: any) => state.schedule.expires);
  const expiresRef = useRef(expires);
  expiresRef.current = expires;

  useEffect(() => {
    dispatch(fetchSchedule());
    const timer = window.setInterval(() => {
      dispatch(scheduleTickTime());
      if (expiresRef.current > (new Date()).getTime()) {
        return;
      }
      dispatch(fetchSchedule());
    }, 1000);
    return () => { window.clearInterval(timer); };
  }, [dispatch]);

  return <Impl />;
}

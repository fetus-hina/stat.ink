import Impl from './myLatestBattles/MyLatestBattles';
import { useEffect, useRef } from 'react';
import { useSelector, useDispatch } from 'react-redux';
import { fetchMyLatestBattles } from '../../actions/myLatestBattles';
import type { IndexRootState, IndexAppDispatch } from '../../store/indexApp';

export default function MyLatestBattles () {
  const dispatch = useDispatch<IndexAppDispatch>();
  const expires = useSelector((state: IndexRootState) => state.myLatestBattles.expires);
  const expiresRef = useRef(expires);
  expiresRef.current = expires;
  const isAvail = useSelector((state: IndexRootState) => Boolean(
    state.myLatestBattles.data &&
    state.myLatestBattles.data.user &&
    state.myLatestBattles.data.battles.length > 0
  ));

  useEffect(() => {
    dispatch(fetchMyLatestBattles());
    const timer = window.setInterval(() => {
      if (expiresRef.current > (new Date()).getTime()) {
        return;
      }
      dispatch(fetchMyLatestBattles());
    }, 60 * 1000);
    return () => { window.clearInterval(timer); };
  }, [dispatch]);

  if (!isAvail) {
    return null;
  }

  return <Impl />;
}

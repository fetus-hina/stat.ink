import Impl from './latestBattles/LatestBattles';
import { useEffect, useRef } from 'react';
import { useSelector, useDispatch } from 'react-redux';
import { fetchLatestBattles } from '../../actions/latestBattles';
import type { IndexRootState, IndexAppDispatch } from '../../store/indexApp';

export default function LatestBattles () {
  const dispatch = useDispatch<IndexAppDispatch>();
  const expires = useSelector((state: IndexRootState) => state.latestBattles.expires);
  const expiresRef = useRef(expires);
  expiresRef.current = expires;
  const isAvail = useSelector((state: IndexRootState) => Boolean(
    state.latestBattles.data &&
    state.latestBattles.data.battles &&
    state.latestBattles.data.battles.length > 0
  ));

  useEffect(() => {
    dispatch(fetchLatestBattles());
    const timer = window.setInterval(() => {
      if (expiresRef.current > (new Date()).getTime()) {
        return;
      }
      dispatch(fetchLatestBattles());
    }, 60 * 1000);
    return () => { window.clearInterval(timer); };
  }, [dispatch]);

  if (!isAvail) {
    return null;
  }

  return <Impl />;
}

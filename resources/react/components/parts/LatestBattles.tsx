import BattleCardListSkeleton from './BattleCardListSkeleton';
import Heading from './latestBattles/Heading';
import Impl from './latestBattles/LatestBattles';
import { useEffect, useRef } from 'react';
import { useSelector, useDispatch } from 'react-redux';
import { fetchLatestBattles } from '../../actions/latestBattles';
import { STATUS_FAILED } from '../../constants';
import type { IndexRootState, IndexAppDispatch } from '../../store/indexApp';

const SKELETON_COUNT = 50;

export default function LatestBattles () {
  const dispatch = useDispatch<IndexAppDispatch>();
  const expires = useSelector((state: IndexRootState) => state.latestBattles.expires);
  const expiresRef = useRef(expires);
  expiresRef.current = expires;
  const hasData = useSelector((state: IndexRootState) => Boolean(
    state.latestBattles.data &&
    state.latestBattles.data.battles &&
    state.latestBattles.data.battles.length > 0
  ));
  const status = useSelector((state: IndexRootState) => state.latestBattles.status);
  const dataIsNull = useSelector((state: IndexRootState) => state.latestBattles.data === null);

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

  if (hasData) {
    return <Impl />;
  }

  if (dataIsNull && status !== STATUS_FAILED) {
    return (
      <div className='mb-3'>
        <Heading />
        <BattleCardListSkeleton count={SKELETON_COUNT} />
      </div>
    );
  }

  return null;
}

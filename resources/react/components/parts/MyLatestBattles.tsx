import BattleCardListSkeleton from './BattleCardListSkeleton';
import Heading from './myLatestBattles/Heading';
import Impl from './myLatestBattles/MyLatestBattles';
import { useEffect, useRef } from 'react';
import { useSelector, useDispatch } from 'react-redux';
import { fetchMyLatestBattles } from '../../actions/myLatestBattles';
import { STATUS_FAILED } from '../../constants';
import type { IndexRootState, IndexAppDispatch } from '../../store/indexApp';

const SKELETON_COUNT = 12;

export default function MyLatestBattles () {
  const dispatch = useDispatch<IndexAppDispatch>();
  const expires = useSelector((state: IndexRootState) => state.myLatestBattles.expires);
  const expiresRef = useRef(expires);
  expiresRef.current = expires;
  const hasData = useSelector((state: IndexRootState) => Boolean(
    state.myLatestBattles.data &&
    state.myLatestBattles.data.user &&
    state.myLatestBattles.data.battles.length > 0
  ));
  const status = useSelector((state: IndexRootState) => state.myLatestBattles.status);
  const dataIsNull = useSelector((state: IndexRootState) => state.myLatestBattles.data === null);

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

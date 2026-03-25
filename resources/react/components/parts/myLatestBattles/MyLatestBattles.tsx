import BattleCardList from '../BattleCardList';
import Heading from './Heading';
import { useSelector } from 'react-redux';
import type { IndexRootState } from '../../../store/indexApp';

export default function MyLatestBattles () {
  const data = useSelector((state: IndexRootState) => state.myLatestBattles.data);

  if (!data) return null;

  return (
    <div className='mb-3'>
      <Heading />
      <BattleCardList
        battles={data.battles}
        fallbackImage={data.images.noImage}
        reltime={data.translations.reltime}
      />
    </div>
  );
}

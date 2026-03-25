import BattleCardList from '../BattleCardList';
import Heading from './Heading';
import React from 'react';
import { useSelector } from 'react-redux';

export default function LatestBattles () {
  const data = useSelector(state => state.latestBattles.data);

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

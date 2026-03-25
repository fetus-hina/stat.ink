import BattleCard from './BattleCard';
import React from 'react';

export default function BattleCardList (props) {
  const { battles, fallbackImage, reltime } = props;

  return (
    <div className='row'>
      {battles.map(battle => (
        <BattleCard
          key={`${battle.variant}-${battle.id}`}
          battle={battle}
          fallbackImage={fallbackImage}
          reltime={reltime}
        />
      ))}
    </div>
  );
}

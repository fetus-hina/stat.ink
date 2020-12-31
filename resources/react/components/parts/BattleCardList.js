import BattleCard from './BattleCard';
import PropTypes from 'prop-types';
import React from 'react';

export default function BattleCardList(props) {
  const { battles, reltime } = props;

  return (
    <div className="row">
      {battles.map(battle => (
        <BattleCard
          key={`${battle.variant}-${battle.id}`}
          battle={battle}
          reltime={reltime}
        />
      ))}
    </div>
  );
}

BattleCardList.propTypes = {
  battles: PropTypes.array.isRequired,
  reltime: PropTypes.object.isRequired,
};

import BattleCard from './BattleCard';

interface BattleCardListProps {
  battles: any[];
  fallbackImage: string;
  reltime: any;
}

export default function BattleCardList (props: BattleCardListProps) {
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

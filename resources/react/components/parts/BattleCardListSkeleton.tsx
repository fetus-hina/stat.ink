import BattleCardSkeleton from './BattleCardSkeleton';

interface BattleCardListSkeletonProps {
  count: number;
}

export default function BattleCardListSkeleton (props: BattleCardListSkeletonProps) {
  const { count } = props;
  const items = Array.from({ length: Math.max(0, count) }, (_, i) => i);

  return (
    <div className='row' role='status' aria-busy='true'>
      {items.map(i => <BattleCardSkeleton key={i} />)}
    </div>
  );
}

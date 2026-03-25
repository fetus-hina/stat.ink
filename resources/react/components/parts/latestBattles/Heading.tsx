import { useSelector } from 'react-redux';
import type { IndexRootState } from '../../../store/indexApp';

export default function Heading () {
  const data = useSelector((state: IndexRootState) => state.latestBattles.data);

  if (!data || !data.translations) return <h2>Recent Battles</h2>;

  const str = data.translations.heading;

  return (
    <h2>{str}</h2>
  );
}

import { useSelector } from 'react-redux';
import type { IndexRootState } from '../../../store/indexApp';

export default function Heading () {
  const apiHeading = useSelector((state: IndexRootState) => state.latestBattles.data?.translations?.heading);
  const bootstrapHeading = useSelector((state: IndexRootState) => state.latestBattles.bootstrap?.heading);

  const heading = apiHeading ?? bootstrapHeading ?? 'Recent Battles';

  return (
    <h2>{heading}</h2>
  );
}

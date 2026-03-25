import { useSelector } from 'react-redux';

export default function Heading () {
  const data = useSelector((state: any) => state.latestBattles.data);
  const str = data && data.translations ? data.translations.heading : 'Recent Battles';

  return (
    <h2>{str}</h2>
  );
}

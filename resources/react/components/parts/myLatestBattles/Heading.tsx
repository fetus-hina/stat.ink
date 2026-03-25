import esc from 'escape-html';
import { useSelector } from 'react-redux';
import type { IndexRootState } from '../../../store/indexApp';

export default function Heading () {
  const data = useSelector((state: IndexRootState) => state.myLatestBattles.data);

  if (!data || !data.translations || !data.user) {
    return <h2>{'Battles'}</h2>;
  }

  const template = data.translations.heading;
  const user = data.user;

  const linkHTML = `<a href="${esc(user.url)}">${esc(user.name)}</a>`;
  const html = esc(template).replace('{name}', linkHTML);

  return (
    <h2 dangerouslySetInnerHTML={{
      __html: html
    }}
    />
  );
}

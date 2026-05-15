import esc from 'escape-html';
import { useSelector } from 'react-redux';
import type { IndexRootState } from '../../../store/indexApp';

export default function Heading () {
  const data = useSelector((state: IndexRootState) => state.myLatestBattles.data);
  const bootstrap = useSelector((state: IndexRootState) => state.myLatestBattles.bootstrap);

  let template: string | null = null;
  let user: { name: string; url: string } | null = null;

  if (data && data.translations && data.user) {
    template = data.translations.heading;
    user = data.user;
  } else if (bootstrap) {
    template = bootstrap.heading;
    user = bootstrap.user;
  }

  if (!template || !user) {
    return <h2>{'Battles'}</h2>;
  }

  const linkHTML = `<a href="${esc(user.url)}">${esc(user.name)}</a>`;
  const html = esc(template).replace('{name}', linkHTML);

  return (
    <h2 dangerouslySetInnerHTML={{
      __html: html
    }}
    />
  );
}

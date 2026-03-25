import React from 'react';
import esc from 'escape-html';
import { useSelector } from 'react-redux';

export default function Heading () {
  const data = useSelector(state => state.myLatestBattles.data);
  const template = data && data.translations ? data.translations.heading : '{name}\'s Battles';
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

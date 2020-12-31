import PropTypes from 'prop-types';
import React from 'react';

export default function RelTime(props) {
  const { now, time, translations } = props;

  const diffSec = Math.floor((now.getTime() - time.getTime()) / 1000);
  return (
    <time dateTime={time.toISOString()}>
      {text(diffSec, translations)}
    </time>
  );
}

RelTime.propTypes = {
  now: PropTypes.object.isRequired,
  time: PropTypes.object.isRequired,
  translations: PropTypes.object.isRequired,
};

const unitMap = [
  [31536000, 'year'],
  [2592000, 'month'],
  [86400, 'day'],
  [3600, 'hour'],
  [60, 'minute'],
  [1, 'second'],
];

function text(diffSec, translations) {
  if (diffSec < 5) {
    return translations.now;
  }

  for (let i = 0; i < unitMap.length; ++i) {
    const [t, key] = unitMap[i];
    if (diffSec >= t) {
      const v = Math.floor(diffSec / t);
      const format = translations[key][v === 1 ? 'one' : 'many'];
      return format.replace('{delta}', v);
    }
  }
}

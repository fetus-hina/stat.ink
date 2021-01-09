import PropTypes from 'prop-types';
import React from 'react';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faFish } from '@fortawesome/free-solid-svg-icons/faFish';
import { faPaintRoller } from '@fortawesome/free-solid-svg-icons/faPaintRoller';
import { faUser } from '@fortawesome/free-solid-svg-icons/faUser';

export default function CounterIcon(props) {
  const { icon } = props;

  switch (icon) {
    case 'battle':
      return <FontAwesomeIcon icon={faPaintRoller} />;

    case 'salmon':
      return <FontAwesomeIcon icon={faFish} />;

    case 'user':
      return <FontAwesomeIcon icon={faUser} />;
  }

  return null;
}

CounterIcon.propTypes = {
  icon: PropTypes.string.isRequired,
};

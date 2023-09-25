import CounterIcon from './CounterIcon';
import CounterDisplay from './CounterDisplay';
import PropTypes from 'prop-types';
import React from 'react';
import { createUseStyles } from 'react-jss';

const useStyles = createUseStyles({
  item: {
  },
  icon: {
    marginRight: '0.618ex'
  },
  label: {
    marginRight: '0.618ex'
  },
  value: {
    whiteSpace: 'nowrap'
  }
});

export default function CounterItem (props) {
  const classes = useStyles();
  const { digit, icon, label, popup, value } = props;

  return (
    <div className={classes.item}>
      <span className={classes.icon}>
        <CounterIcon icon={icon} />
      </span>
      <span className={classes.label}>
        {label}:
      </span>
      <span className={classes.value} title={popup}>
        <CounterDisplay
          digit={digit}
          value={value}
        />
      </span>
    </div>
  );
}

CounterItem.propTypes = {
  digit: PropTypes.number.isRequired,
  icon: PropTypes.string.isRequired,
  label: PropTypes.string.isRequired,
  popup: PropTypes.string,
  value: PropTypes.number
};

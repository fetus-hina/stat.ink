import PropTypes from 'prop-types';
import React from 'react';
import { createUseStyles } from 'react-jss';

const useStyles = createUseStyles({
  root: {
    display: 'inline-block',
    fontFamily: 'DSEG7-Classic, fantasy',
    fontSize: '1.618em',
    fontStyle: 'italic',
    fontWeight: '400',
    position: 'relative'
  },
  padding: {
    color: 'transparent'
  },
  bgNumber: {
    color: 'rgba(0, 0, 0, 0.1)',
    display: 'inline-block',
    left: '0',
    position: 'absolute',
    top: '0',
    zIndex: '-1',

    'body.theme-dark &': {
      color: 'rgba(255, 255, 255, 0.05)'
    }
  }
});

export default function CounterDisplay (props) {
  const { value, digit } = props;
  const classes = useStyles();

  const strValue = value !== null ? String(value) : '-'.repeat(digit);
  const padding = (strValue.length < digit)
    ? (
      <span className={classes.padding} aria-hidden='true'>
        {'!'.repeat(digit - strValue.length)}
      </span>
      )
    : null;

  return (
    <span className={classes.root}>
      <span className={classes.bgNumber} aria-hidden='true'>
        {'8'.repeat(digit)}
      </span>
      <span>
        {padding}{strValue}
      </span>
    </span>
  );
}

CounterDisplay.propTypes = {
  digit: PropTypes.number.isRequired,
  value: PropTypes.number
};

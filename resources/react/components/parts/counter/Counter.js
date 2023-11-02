import CounterItem from './CounterItem';
import PropTypes from 'prop-types';
import React from 'react';
import { STATUS_OK } from '../../../constants';
import { connect } from 'react-redux';
import { createUseStyles } from 'react-jss';

const DEFAULT_DIGITS = 8;

const useStyles = createUseStyles({
  root: {
    fontSize: '16px',
    lineHeight: '1.3',
    margin: '0',
    textAlign: 'right'
  },
  initializing: {
    minHeight: '104px'
  }
});

const isEmptyObject = (obj) => Object.keys(obj).length === 0;

function Counter (props) {
  const classes = useStyles();
  const { data, status } = props;

  const rows = createRows(data);
  const maxDigit = rows
    .map(v => v.count !== null ? String(v.count).length : DEFAULT_DIGITS)
    .reduce((acc, cur) => Math.max(acc, cur), DEFAULT_DIGITS);

  return (
    <aside
      className={
        [
          classes.root,
          status !== STATUS_OK && isEmptyObject(data) ? classes.initializing : null
        ]
          .filter(v => v !== null)
          .join(' ')
      }
    >
      {rows.map(v => (
        <CounterItem
          key={v.type}
          digit={maxDigit}
          category={v.type}
          icon={v.icon}
          label={v.label}
          popup={v.popup}
          value={v.count}
        />
      ))}
    </aside>
  );
}

Counter.propTypes = {
  data: PropTypes.object.isRequired,
  status: PropTypes.string.isRequired
};

function mapStateToProps (state) {
  return {
    data: state.counter.data,
    status: state.counter.status
  };
}

function mapDispatchToProps (/* dispatch */) {
  return {};
}

export default connect(mapStateToProps, mapDispatchToProps)(Counter);

const numberFormat = (number) => Number(number)
  .toLocaleString(window?.document?.documentElement?.lang ?? 'en-US');

function createRows (jsonData) {
  const results = [
    {
      type: 'user',
      icon: null,
      label: 'Users',
      popup: null,
      count: null
    },
    {
      type: 'battle',
      icon: null,
      label: 'Battles',
      popup: null,
      count: null
    },
    {
      type: 'salmon',
      icon: null,
      label: 'Shifts',
      popup: null,
      count: null
    }
  ];

  results.forEach(currentRow => {
    const typeMatchedJsonItems = Object.entries(jsonData)
      .filter(([, v]) => (v.type === currentRow.type))
      .sort(([a], [b]) => a.localeCompare(b));
    if (typeMatchedJsonItems.length > 0) {
      currentRow.icon = typeMatchedJsonItems[0][1].icon;
      currentRow.label = typeMatchedJsonItems[0][1].label;
      currentRow.count = typeMatchedJsonItems
        .map(([, v]) => Number(v.count))
        .reduce((acc, cur) => acc + cur, 0);
      currentRow.popup = typeMatchedJsonItems.length > 1
        ? typeMatchedJsonItems
          .map(([, v]) => numberFormat(v.count))
          .join(' + ')
        : null;
    }
  });

  return results;
}

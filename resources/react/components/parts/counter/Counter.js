import CounterItem from './CounterItem';
import PropTypes from 'prop-types';
import React from 'react';
import { connect } from 'react-redux';
import { createUseStyles } from 'react-jss';

const TYPE_ORDER = [
  'user',
  'battle',
  'salmon'
];

const useStyles = createUseStyles({
  root: {
    fontSize: '16px',
    lineHeight: '1.3',
    margin: '0 0 10px',
    textAlign: 'right'
  }
});

function Counter (props) {
  const classes = useStyles();
  const { data } = props;

  const rows = createRows(data);
  if (rows.length < 1) {
    return null;
  }

  const maxDigit = rows
    .map(v => String(v.count).length)
    .reduce((acc, cur) => Math.max(acc, cur), 1);

  return (
    <aside className={classes.root}>
      {rows.map(v => (
        <CounterItem
          key={v.type}
          digit={maxDigit}
          icon={v.type}
          label={v.label}
          popup={v.popup}
          value={v.count}
        />
      ))}
    </aside>
  );
}

Counter.propTypes = {
  data: PropTypes.object.isRequired
};

function mapStateToProps (state) {
  return {
    data: state.counter.data
  };
}

function mapDispatchToProps (/* dispatch */) {
  return {};
}

export default connect(mapStateToProps, mapDispatchToProps)(Counter);

function createRows (jsonData) {
  const results = [];

  TYPE_ORDER.map(type => {
    // get filtered list (by type)
    const typeData = Object.entries(jsonData)
      .filter(
        ([, v]) => (v.type === type)
      )
      .sort(
        ([a], [b]) => a.localeCompare(b)
      );
    if (typeData.length > 0) {
      const [, info] = typeData[0];
      results.push({
        type,
        label: info.label,
        popup: typeData.length > 1
          ? typeData.map(([, v]) => String(v.count)).join(' + ') // TODO: number format
          : null,
        count: typeData.map(([, v]) => Number(v.count)).reduce((acc, cur) => acc + cur, 0)
      });
    }
    return null;
  });

  return results;
}

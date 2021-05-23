import PropTypes from 'prop-types';
import React from 'react';
import { connect } from 'react-redux';
import { createUseStyles } from 'react-jss';

const useStyles = createUseStyles({
  pointer: {
    cursor: 'pointer'
  },
  modeIcon: {
    height: '1.1em',
    maxWidth: 'none',
    verticalAlign: 'baseline',
    width: 'auto'
  }
});

function ScheduleTab (props) {
  const { isSelected, item, onChanged, schedule } = props;
  const classes = useStyles();
  const mode = extractMode(schedule, item);

  return (
    <li
      className={isSelected ? 'active' : null}
      role='tab'
      aria-controls='schedule-content'
      aria-selected={isSelected}
    >
      <a className={classes.pointer} onClick={() => { onChanged(item.id); }}>
        {label(classes, mode, props)}
      </a>
    </li>
  );
}

ScheduleTab.propTypes = {
  isSelected: PropTypes.bool.isRequired,
  item: PropTypes.object.isRequired,
  now: PropTypes.number.isRequired,
  onChanged: PropTypes.func.isRequired,
  schedule: PropTypes.object,
  translations: PropTypes.object
};

function label (classes, mode, props) {
  const { item, now, translations } = props;

  if (!mode) {
    return item.label;
  }

  const current = extractCurrent(mode, now);

  return (
    <>
      {mode.image
        ? (
          <>
            <img src={mode.image} className={classes.modeIcon} />
            {' '}
          </>
          )
        : null}
      {current && current.rule && current.rule.icon
        ? (
          <>
            <img alt={current.rule.name} className={classes.modeIcon} src={current.rule.icon} title={current.rule.name} />
            {' '}
          </>
          )
        : null}
      {current && current.weapons
        ? (
          <>
            <span className='text-warning' title={translations ? translations.salmon_open : 'Open!'}>
              <span className='fas fa-fw fa-certificate' />
            </span>
            {' '}
          </>
          )
        : null}
      {mode.name}
    </>
  );
}

function extractMode (schedule, tabItem) {
  const ref = tabItem.ref.slice(); // ["splatoon2", "regular"]
  let current = Object.assign({}, schedule);
  while (current && ref.length > 0) {
    const curRef = ref.shift();
    if (!current[curRef]) {
      return null;
    }
    current = current[curRef];
  }
  return current;
}

function extractCurrent (mode, now) {
  if (!mode || !mode.schedules) {
    return null;
  }

  const matches = mode.schedules.filter((item) => {
    return item.time[0] <= now && now < item.time[1];
  });
  if (matches.length !== 1) {
    return null;
  }

  const current = matches.shift();
  return current;
}

function mapStateToProps (state) {
  return {
    now: Math.floor(state.schedule.currentTime / 1000),
    schedule: state.schedule.data,
    translations: state.schedule.data ? state.schedule.data.translations : null
  };
}

function mapDispatchToProps (/* dispatch */) {
  return {};
}

export default connect(mapStateToProps, mapDispatchToProps)(ScheduleTab);

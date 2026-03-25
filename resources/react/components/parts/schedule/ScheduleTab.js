import React from 'react';
import classes from './ScheduleTab.module.css';
import { connect } from 'react-redux';

function ScheduleTab (props) {
  const { isSelected, item, onChanged, schedule } = props;
  const mode = extractMode(schedule, item);

  if (!mode || !mode.schedules || mode.schedules.length < 1) {
    return null;
  }

  return (
    <li
      className={isSelected ? 'active' : null}
      role='tab'
      aria-controls='schedule-content'
      aria-selected={isSelected}
    >
      <a className={classes.pointer} onClick={() => { onChanged(item.id); }}>
        {label(mode, props)}
      </a>
    </li>
  );
}

function label (mode, props) {
  const { gameIcons, isSelected, item, now, translations } = props;

  if (!mode) {
    return item.label;
  }

  const current = extractCurrent(mode, now);

  return (
    <>
      {mode.game && gameIcons[mode.game] && gameIcons[mode.game].icon
        ? (
          <>
            <img
              alt={gameIcons[mode.game].name}
              className={classes.modeIcon}
              src={gameIcons[mode.game].icon}
              title={gameIcons[mode.game].name}
            />
            {' '}
          </>
          )
        : null}
      {mode.image
        ? (
          <>
            <img
              alt=''
              className={classes.modeIcon}
              src={mode.image}
              title={mode.name}
            />
            {' '}
          </>
          )
        : null}
      {current?.rule?.icon
        ? (
          <>
            <img
              alt={current.rule.name}
              className={classes.modeIcon}
              src={current.rule.icon}
              title={current.rule.name}
            />
            {' '}
          </>
          )
        : null}
      {current?.king?.image
        ? (
          <>
            <img
              alt={current.king.name}
              className={classes.modeIcon}
              src={current.king.image}
              title={current.king.name}
            />
            {' '}
          </>
          )
        : null}
      {current?.weapons && item.showOpen
        ? (
          <>
            <span className='text-warning' title={translations ? translations.salmon_open : 'Open!'}>
              <span className='fas fa-fw fa-certificate' />
            </span>
            {' '}
          </>
          )
        : null}
      {isSelected ? mode.name : null}
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
    gameIcons: state.schedule.data ? state.schedule.data.games : null,
    now: Math.floor(state.schedule.currentTime / 1000),
    schedule: state.schedule.data,
    translations: state.schedule.data ? state.schedule.data.translations : null
  };
}

function mapDispatchToProps (/* dispatch */) {
  return {};
}

export default connect(mapStateToProps, mapDispatchToProps)(ScheduleTab);

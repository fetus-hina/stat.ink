import PropTypes from 'prop-types';
import React from 'react';
import ScheduleCard from './ScheduleCard';
import ScheduleContentHeading from './ScheduleContentHeading';
import { connect } from 'react-redux';
import { createUseStyles } from 'react-jss';

const useStyles = createUseStyles({
  schedule: {
    flex: '1 1 100%',
    marginLeft: '15px',
    marginRight: '15px',
    maxWidth: '100%',

    '@media (min-width: 992px)': {
      maxWidth: 'calc(50% - 30px)'
    }
  },
  cards: {
    display: 'flex',
    flexWrap: 'nowrap',
    marginLeft: '-15px',
    marginRight: '-15px'
  },
  card: {
    flex: '1 1 50%',
    marginLeft: '15px',
    marginRight: '15px'
  }
});

function ScheduleContent (props) {
  const { mode, modeIcon } = props;
  const classes = useStyles();
  const schedules = getDisplayTargetSchedules(props);

  return schedules.map((sc, i) => (
    <div key={i} className={[classes.schedule, 'mb-3'].join(' ')}>
      <ScheduleContentHeading schedule={sc} mode={mode} />
      <div className={classes.cards}>
        {sc.maps.map((mapInfo) => (
          <div className={classes.card} key={mapInfo.key}>
            <ScheduleCard
              map={mapInfo}
              mode={mode}
              modeIcon={modeIcon}
              schedule={sc}
            />
          </div>
        ))}
      </div>
    </div>
  ));
}

ScheduleContent.propTypes = {
  locale: PropTypes.object,
  mode: PropTypes.string.isRequired,
  modeIcon: PropTypes.string,
  now: PropTypes.number.isRequired,
  schedules: PropTypes.array.isRequired
};

function getDisplayTargetSchedules (props) {
  const { now, schedules } = props;
  const tmpList = schedules.filter(item => item.time[1] > now);
  tmpList.sort((a, b) => a.time[1] - b.time[1]);
  return tmpList.slice(0, 2);
}

function mapStateToProps (state) {
  return {
    locale: state.schedule.data ? state.schedule.data.locale : null,
    now: Math.floor(state.schedule.currentTime / 1000)
  };
}

function mapDispatchToProps (/* dispatch */) {
  return {};
}

export default connect(mapStateToProps, mapDispatchToProps)(ScheduleContent);

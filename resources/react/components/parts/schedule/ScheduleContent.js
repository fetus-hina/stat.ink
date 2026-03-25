import React from 'react';
import ScheduleCard from './ScheduleCard';
import ScheduleContentHeading from './ScheduleContentHeading';
import classes from './ScheduleContent.module.css';
import { useSelector } from 'react-redux';

export default function ScheduleContent (props) {
  const { mode, modeIcon, schedules } = props;
  const now = useSelector(state => Math.floor(state.schedule.currentTime / 1000));

  const displaySchedules = getDisplayTargetSchedules(schedules, now);

  return displaySchedules.map((sc, i) => (
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

function getDisplayTargetSchedules (schedules, now) {
  const tmpList = schedules.filter(item => item.time[1] > now);
  tmpList.sort((a, b) => a.time[1] - b.time[1]);
  return tmpList.slice(0, 2);
}

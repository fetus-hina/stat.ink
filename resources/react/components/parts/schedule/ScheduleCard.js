import PropTypes from 'prop-types';
import React from 'react';
import classes from './ScheduleCard.module.css';

export default function ScheduleCard (props) {
  const { map, mode, modeIcon, schedule } = props;
  const isSalmon = String(mode).startsWith('salmon');
  const isEggstra = mode === 'salmon_eggstra' && modeIcon;

  return (
    <div className={[classes.root, classes.outlined].join(' ')}>
      <div
        className={[
          classes.media,
          isSalmon ? classes.media32x9 : classes.media16x9
        ].join(' ')}
        style={{
          backgroundImage: `url(${map.image})`
        }}
      >
        {isSalmon && schedule && schedule.weapons
          ? (
            <div className={classes.weapons}>
              <ul>
                {schedule.weapons.map((weapon, i) => (
                  <li key={weapon.key + '-' + i}>
                    {(weapon.key === 'random' && !weapon.icon)
                      ? <span
                          className='fas fa-question fa-fw'
                          title={weapon.name}
                        />
                      : <img
                          alt={weapon.name}
                          src={weapon.icon}
                          title={weapon.name}
                        />}
                  </li>
                ))}
              </ul>
            </div>
            )
          : null}
        {isSalmon && (isEggstra || schedule?.is_big_run || schedule?.king?.image)
          ? (
            <div className={classes.leftTopInfo}>
              {isEggstra
                ? <img
                    src={modeIcon}
                    alt=''
                    title=''
                  />
                : null}
              {schedule?.king?.image
                ? <img
                    src={schedule?.king?.image}
                    alt={schedule?.king?.name}
                    title={schedule?.king?.name}
                  />
                : null}
              {schedule?.is_big_run
                ? <span className={classes.bigRun}> Big Run</span>
                : null}
            </div>
            )
          : null}
      </div>
      <div className={classes.content}>
        {map.name}
      </div>
    </div>
  );
}

ScheduleCard.propTypes = {
  map: PropTypes.object.isRequired,
  mode: PropTypes.string.isRequired,
  modeIcon: PropTypes.string,
  schedule: PropTypes.object.isRequired
};

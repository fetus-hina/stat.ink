import PropTypes from 'prop-types';
import React from 'react';
import { createUseStyles } from 'react-jss';

const useStyles = createUseStyles({
  root: {
    backgroundColor: '#fff',
    borderRadius: '4px',
    boxShadow: [
      '0 2px 1px -1px rgb(0 0 0 / 20%)',
      '0 1px 1px 0 rgb(0 0 0 / 14%)',
      '0 1px 3px 0 rgb(0 0 0 / 12%)'
    ].join(', '),
    color: '#333',
    overflow: 'hidden',
    transition: 'box-shadow 300ms cubic-bezier(0.4, 0, 0.2, 1) 0ms'
  },
  outlined: {
    // border: '1px solid rgba(0, 0, 0, 0.12)',
    border: '1px solid #ddd'
  },
  media: {
    backgroundClip: 'padding-box',
    backgroundColor: 'transparent',
    backgroundOrigin: 'padding-box',
    backgroundPosition: '50% 50%',
    backgroundRepeat: 'no-repeat',
    backgroundSize: 'cover',
    display: 'block',
    margin: '0',
    overflow: 'hidden',
    padding: '0',
    position: 'relative',
    width: '100%'
  },
  media16x9: {
    '&::before': {
      display: 'block',
      paddingTop: 'calc(9 / 16 * 100%)',
      content: '""'
    }
  },
  media32x9: {
    '&::before': {
      display: 'block',
      paddingTop: 'calc(9 / 32 * 100%)',
      content: '""'
    }
  },
  content: {
    padding: '15px'
  },
  weapons: {
    backgroundColor: 'rgba(255, 255, 255, 0.8)',
    borderTopLeftRadius: '4px',
    bottom: '0',
    color: '#ccc',
    fontSize: '24px',
    lineHeight: '1',
    margin: '0',
    padding: '8px',
    position: 'absolute',
    right: '0',

    '& img': {
      height: '24px',
      width: 'auto'
    },
    '& ul, & li': {
      display: 'inline-block',
      listStyleImage: 'none',
      listStyleType: 'none',
      margin: '0',
      padding: '0',
      lineHeight: '1'
    },
    '& li': {
      display: 'inline',
      marginRight: '0.5em',

      '&:last-child': {
        marginRight: '0'
      }
    }
  },
  bigRun: {
    backgroundColor: 'rgba(0, 0, 0, 0.8)',
    borderBottomRightRadius: '4px',
    color: '#dd0',
    fontFamily: 'paintball,cursive',
    fontSize: '24px',
    fontStyle: 'normal',
    fontWeight: '400',
    left: '0',
    lineHeight: '1',
    margin: '0',
    padding: '8px',
    position: 'absolute',
    top: '0'
  }
});

export default function ScheduleCard (props) {
  const { map, mode, schedule } = props;
  const isSalmon = (mode === 'salmon');
  const classes = useStyles();

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
        {isSalmon && schedule && schedule.is_big_run
          ? (
            <div className={classes.bigRun}>
              Big Run
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
  schedule: PropTypes.object.isRequired
};

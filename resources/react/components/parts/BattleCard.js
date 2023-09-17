import PropTypes from 'prop-types';
import React from 'react';
import RelTime from './RelTime';
import { createUseStyles } from 'react-jss';

const EMPTY_IMAGE_16_BY_9 =
  'iVBORw0KGgoAAAANSUhEUgAAABAAAAAJAQMAAAAB5D5xAAAAA1BMVEX///+nxBvIAAAAAXRSTlMA' +
  'QObYZgAAAAlwSFlzAAAOxAAADsQBlSsOGwAAAApJREFUCB1jwA0AABsAAScKbaoAAAAASUVORK5C' +
  'YII=';

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
    border: '1px solid #ddd'
  },
  link: {
    '&:hover $media': {
      filter: 'brightness(110%)'
    }
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
  mediaSplatnet2: {
    // scale: 1138 / 1024,
  },
  modeIcons: {
    backgroundColor: 'rgba(255, 255, 255, 0.8)',
    borderBottomRightRadius: '4px',
    left: '0',
    padding: '4px',
    position: 'absolute',
    top: '0'
  },
  time: {
    backgroundColor: 'rgba(255, 255, 255, 0.8)',
    borderBottomLeftRadius: '4px',
    color: '#333',
    fontSize: '10px',
    right: '0',
    padding: '4px',
    position: 'absolute',
    top: '0'
  },
  content: {
    alignItems: 'flex-start',
    display: 'flex',
    overflowX: 'hidden',
    padding: '10px 15px'
  },
  userIcon: {
    backgroundColor: '#fff',
    border: '1px solid #ccc',
    borderRadius: '4px',
    flex: '0 0 48px',
    height: '48px',
    marginRight: '10px',
    width: '48px'
  },
  contentData: {
    display: 'flex',
    flex: '1 1 100%',
    flexDirection: 'column'
  },
  ellipsis: {
    overflow: 'hidden',
    textOverflow: 'ellipsis',
    whiteSpace: 'nowrap'
  }
});

const nbsp = '\u{00a0}';

export default function BattleCard (props) {
  const { battle, fallbackImage, reltime } = props;
  const classes = useStyles();
  const bgImages = buildImages(battle, fallbackImage);

  return (
    <div className='col-xs-12 col-sm-6 col-md-4 col-lg-3 mb-2'>
      <div className={[classes.root, classes.outlined].join(' ')}>
        <a href={battle.url} className={classes.link}>
          <div
            className={
              [
                classes.media,
                classes.media16x9,
                (battle.variant === 'splatoon2' && battle.image) ? classes.mediaSplatnet2 : null
              ].join(' ')
            }
            style={{
              backgroundImage: bgImages.join(', ')
            }}
          >
            {((battle.mode && battle.mode.icon) || (battle.rule && battle.rule.icon))
              ? (
                <div className={classes.modeIcons}>
                  {battle.mode && battle.mode.icon
                    ? <img
                        alt={battle.mode.name}
                        height={24}
                        src={battle.mode.icon}
                        title={battle.mode.name}
                      />
                    : null}
                  {battle.rule && battle.rule.icon
                    ? <img
                        alt={battle.rule.name}
                        height={24}
                        src={battle.rule.icon}
                        title={battle.rule.name}
                      />
                    : null}
                </div>
                )
              : null}
            <div className={classes.time}>
              <RelTime
                translations={reltime}
                now={new Date()}
                time={new Date(battle.time * 1000)}
              />
            </div>
          </div>
        </a>
        <div className={classes.content}>
          <div className={classes.userIcon}>
            <a href={battle.user.url}>
              <img
                alt={battle.user.name}
                height={46}
                src={battle.user.icon[0]}
                title=''
                width={46}
              />
            </a>
          </div>
          <div className={classes.contentData}>
            <div className={['text-muted', 'small', classes.ellipsis].join(' ')}>
              {battle.summary || nbsp}
            </div>
            <div className={['text-muted', 'small', classes.ellipsis].join(' ')}>
              {battle.summary2 || nbsp}
            </div>
            <div className={classes.ellipsis}>
              <a href={battle.user.url}>
                {battle.user.name}
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

BattleCard.propTypes = {
  battle: PropTypes.object.isRequired,
  fallbackImage: PropTypes.string,
  reltime: PropTypes.object.isRequired
};

function buildImages (battle, fallbackImage) {
  const results = [];
  if (battle.image) {
    results.push(`url(${battle.image})`);
  }
  if (battle.stage) {
    if (battle.isWin === true) {
      results.push(`url(${battle.stage.image.win})`);
    } else if (battle.isWin === false) {
      results.push(`url(${battle.stage.image.lose})`);
    } else {
      results.push(`url(${battle.stage.image.normal})`);
    }
  }
  if (fallbackImage) {
    results.push(`url(${fallbackImage})`);
  }
  results.push(`url(data:image/png;base64,${EMPTY_IMAGE_16_BY_9})`);
  return results;
}

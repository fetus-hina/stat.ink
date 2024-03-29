import PropTypes from 'prop-types';
import React from 'react';
import RelTime from './RelTime';
import { createUseStyles } from 'react-jss';

const EMPTY_IMAGE_16_BY_9 =
  'iVBORw0KGgoAAAANSUhEUgAAABAAAAAJAQMAAAAB5D5xAAAAA1BMVEX///+nxBvIAAAAAXRSTlMA' +
  'QObYZgAAAAlwSFlzAAAOxAAADsQBlSsOGwAAAApJREFUCB1jwA0AABsAAScKbaoAAAAASUVORK5C' +
  'YII=';

const CONTENT_PADDING_X = '15px';
const USER_ICON_SIZE = '48px';
const USER_ICON_MARGIN_X = '10px';

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

  mediaHasThumbnail: {
    '@media (min-width: 768px)': {
      backgroundImage: [
        'image-set(var(--thumbnail-sm-1-avif) 1x type("image/avif"), var(--thumbnail-sm-2-avif) 2x type("image/avif"), var(--thumbnail-sm-1-webp) 1x type("image/webp"), var(--thumbnail-sm-2-webp) 2x type("image/webp"), var(--thumbnail-sm-1-jpg) 1x type("image/jpeg"), var(--thumbnail-sm-2-jpg) 2x type("image/jpeg"))',
        'var(--thumbnail-fallback)',
        'linear-gradient(to bottom, #ddd, #bbb)',
        `url(data:image/png;base64,${EMPTY_IMAGE_16_BY_9})`
      ].join(', ') + ' !important'
    },
    '@media (min-width: 992px)': {
      backgroundImage: [
        'image-set(var(--thumbnail-md-1-avif) 1x type("image/avif"), var(--thumbnail-md-2-avif) 2x type("image/avif"), var(--thumbnail-md-1-webp) 1x type("image/webp"), var(--thumbnail-md-2-webp) 2x type("image/webp"), var(--thumbnail-md-1-jpg) 1x type("image/jpeg"), var(--thumbnail-md-2-jpg) 2x type("image/jpeg"))',
        'var(--thumbnail-fallback)',
        'linear-gradient(to bottom, #ddd, #bbb)',
        `url(data:image/png;base64,${EMPTY_IMAGE_16_BY_9})`
      ].join(', ') + ' !important'
    },
    '@media (min-width: 1200px)': {
      backgroundImage: [
        'image-set(var(--thumbnail-lg-1-avif) 1x type("image/avif"), var(--thumbnail-lg-2-avif) 2x type("image/avif"), var(--thumbnail-lg-1-webp) 1x type("image/webp"), var(--thumbnail-lg-2-webp) 2x type("image/webp"), var(--thumbnail-lg-1-jpg) 1x type("image/jpeg"), var(--thumbnail-lg-2-jpg) 2x type("image/jpeg"))',
        'var(--thumbnail-fallback)',
        'linear-gradient(to bottom, #ddd, #bbb)',
        `url(data:image/png;base64,${EMPTY_IMAGE_16_BY_9})`
      ].join(', ') + ' !important'
    },

    // Safari doesn't support image-set type() syntax until v17
    // https://caniuse.com/?search=image-set
    '.apple &': {
      '@media (min-width: 768px)': {
        backgroundImage: [
          'image-set(var(--thumbnail-sm-1-avif) 1x, var(--thumbnail-sm-2-avif) 2x)',
          'var(--thumbnail-fallback)',
          'linear-gradient(to bottom, #ddd, #bbb)',
          `url(data:image/png;base64,${EMPTY_IMAGE_16_BY_9})`
        ].join(', ') + ' !important'
      },
      '@media (min-width: 992px)': {
        backgroundImage: [
          'image-set(var(--thumbnail-md-1-avif) 1x, var(--thumbnail-md-2-avif) 2x)',
          'var(--thumbnail-fallback)',
          'linear-gradient(to bottom, #ddd, #bbb)',
          `url(data:image/png;base64,${EMPTY_IMAGE_16_BY_9})`
        ].join(', ') + ' !important'
      },
      '@media (min-width: 1200px)': {
        backgroundImage: [
          'image-set(var(--thumbnail-lg-1-avif) 1x, var(--thumbnail-lg-2-avif) 2x)',
          'var(--thumbnail-fallback)',
          'linear-gradient(to bottom, #ddd, #bbb)',
          `url(data:image/png;base64,${EMPTY_IMAGE_16_BY_9})`
        ].join(', ') + ' !important'
      }
    }
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
    padding: `10px ${CONTENT_PADDING_X}`
  },
  userIcon: {
    backgroundColor: '#fff',
    border: '1px solid #ccc',
    borderRadius: '4px',
    flex: `0 0 ${USER_ICON_SIZE}`,
    height: USER_ICON_SIZE,
    marginRight: USER_ICON_MARGIN_X,
    width: USER_ICON_SIZE
  },
  contentData: {
    display: 'flex',
    flex: '1 1 100%',
    flexDirection: 'column',
    width: `calc(100% - (${CONTENT_PADDING_X} * 2 + ${USER_ICON_SIZE} + ${USER_ICON_MARGIN_X}))`
  },
  ellipsis: {
    overflow: 'hidden',
    textOverflow: 'ellipsis',
    whiteSpace: 'nowrap'
  }
});

const useStyles2 = createUseStyles({
  mediaBackground: ({ battle, fallbackImage }) => ({
    backgroundImage: buildBackgroundImages(battle, fallbackImage)
  })
});

const nbsp = '\u{00a0}';

function thumbnailUrl (template, width, height, x, ext) {
  return template
    .replace('<w>', Math.floor(width * x))
    .replace('<h>', Math.floor(height * x))
    .replace(/jpg$/, ext);
}

export default function BattleCard (props) {
  const { battle, fallbackImage, reltime } = props;
  const classes = useStyles();
  const classes2 = useStyles2(props);

  return (
    <div className='col-xs-12 col-sm-6 col-md-4 col-lg-3 mb-2'>
      <div className={[classes.root, classes.outlined].join(' ')}>
        <a href={battle.url} className={classes.link}>
          <div
            className={
              [
                classes.media,
                classes.media16x9,
                classes2.mediaBackground,
                battle.thumbnail ? classes.mediaHasThumbnail : null
              ].filter(v => v !== null).join(' ')
            }
            style={
              battle.thumbnail
                ? {
                    '--thumbnail-lg-1-avif': `url('${thumbnailUrl(battle.thumbnail, 260.50, 146.53, 1, 'avif')}')`,
                    '--thumbnail-lg-1-jpg': `url('${thumbnailUrl(battle.thumbnail, 260.50, 146.53, 1, 'jpg')}')`,
                    '--thumbnail-lg-1-webp': `url('${thumbnailUrl(battle.thumbnail, 260.50, 146.53, 1, 'webp')}')`,
                    '--thumbnail-lg-2-avif': `url('${thumbnailUrl(battle.thumbnail, 260.50, 146.53, 2, 'avif')}')`,
                    '--thumbnail-lg-2-jpg': `url('${thumbnailUrl(battle.thumbnail, 260.50, 146.53, 2, 'jpg')}')`,
                    '--thumbnail-lg-2-webp': `url('${thumbnailUrl(battle.thumbnail, 260.50, 146.53, 2, 'webp')}')`,
                    '--thumbnail-md-1-avif': `url('${thumbnailUrl(battle.thumbnail, 291.33, 163.86, 1, 'avif')}')`,
                    '--thumbnail-md-1-jpg': `url('${thumbnailUrl(battle.thumbnail, 291.33, 163.86, 1, 'jpg')}')`,
                    '--thumbnail-md-1-webp': `url('${thumbnailUrl(battle.thumbnail, 291.33, 163.86, 1, 'webp')}')`,
                    '--thumbnail-md-2-avif': `url('${thumbnailUrl(battle.thumbnail, 291.33, 163.86, 2, 'avif')}')`,
                    '--thumbnail-md-2-jpg': `url('${thumbnailUrl(battle.thumbnail, 291.33, 163.86, 2, 'jpg')}')`,
                    '--thumbnail-md-2-webp': `url('${thumbnailUrl(battle.thumbnail, 291.33, 163.86, 2, 'webp')}')`,
                    '--thumbnail-sm-1-avif': `url('${thumbnailUrl(battle.thumbnail, 343.00, 192.94, 1, 'avif')}')`,
                    '--thumbnail-sm-1-jpg': `url('${thumbnailUrl(battle.thumbnail, 343.00, 192.94, 1, 'jpg')}')`,
                    '--thumbnail-sm-1-webp': `url('${thumbnailUrl(battle.thumbnail, 343.00, 192.94, 1, 'webp')}')`,
                    '--thumbnail-sm-2-avif': `url('${thumbnailUrl(battle.thumbnail, 343.00, 192.94, 2, 'avif')}')`,
                    '--thumbnail-sm-2-jpg': `url('${thumbnailUrl(battle.thumbnail, 343.00, 192.94, 2, 'jpg')}')`,
                    '--thumbnail-sm-2-webp': `url('${thumbnailUrl(battle.thumbnail, 343.00, 192.94, 2, 'webp')}')`,
                    '--thumbnail-fallback': `url('${fallbackImage}')`
                  }
                : {}
            }
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

function buildBackgroundImages (battle, fallbackImage) {
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

  return results.join(', ');
}

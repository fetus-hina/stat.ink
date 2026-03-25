import PropTypes from 'prop-types';
import React from 'react';
import RelTime from './RelTime';
import classes from './BattleCard.module.css';

const EMPTY_IMAGE_16_BY_9 =
  'iVBORw0KGgoAAAANSUhEUgAAABAAAAAJAQMAAAAB5D5xAAAAA1BMVEX///+nxBvIAAAAAXRSTlMA' +
  'QObYZgAAAAlwSFlzAAAOxAAADsQBlSsOGwAAAApJREFUCB1jwA0AABsAAScKbaoAAAAASUVORK5C' +
  'YII=';

const nbsp = '\u{00a0}';

function thumbnailUrl (template, width, height, x, ext) {
  return template
    .replace('<w>', Math.floor(width * x))
    .replace('<h>', Math.floor(height * x))
    .replace(/jpg$/, ext);
}

export default function BattleCard (props) {
  const { battle, fallbackImage, reltime } = props;

  return (
    <div className='col-xs-12 col-sm-6 col-md-4 col-lg-3 mb-2'>
      <div className={[classes.root, classes.outlined].join(' ')}>
        <a href={battle.url} className={classes.link}>
          <div
            className={
              [
                classes.media,
                classes.media16x9,
                battle.thumbnail ? classes.mediaHasThumbnail : null
              ].filter(v => v !== null).join(' ')
            }
            style={Object.assign(
              { backgroundImage: buildBackgroundImages(battle, fallbackImage) },
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
            )}
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

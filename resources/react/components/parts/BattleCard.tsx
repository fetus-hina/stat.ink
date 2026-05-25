import type { Battle, RelTimeTranslations } from '../../types';
import type { CSSProperties } from 'react';
import { useState } from 'react';
import RelTime from './RelTime';
import classes from './BattleCard.module.css';

const EMPTY_IMAGE_16_BY_9 =
  'iVBORw0KGgoAAAANSUhEUgAAABAAAAAJAQMAAAAB5D5xAAAAA1BMVEX///+nxBvIAAAAAXRSTlMA' +
  'QObYZgAAAAlwSFlzAAAOxAAADsQBlSsOGwAAAApJREFUCB1jwA0AABsAAScKbaoAAAAASUVORK5C' +
  'YII=';

const nbsp = '\u{00a0}';

const THUMBNAIL_SIZES = '(min-width: 1200px) 260.5px, (min-width: 992px) 291.33px, 343px';

function thumbnailUrl (template: string, width: number, height: number, x: number, ext: string) {
  return template
    .replace('<w>', String(Math.floor(width * x)))
    .replace('<h>', String(Math.floor(height * x)))
    .replace(/jpg$/, ext);
}

function buildThumbnailSrcSet (template: string, ext: string) {
  return [
    `${thumbnailUrl(template, 260.50, 146.53, 1, ext)} 260w`,
    `${thumbnailUrl(template, 260.50, 146.53, 2, ext)} 521w`,
    `${thumbnailUrl(template, 291.33, 163.86, 1, ext)} 291w`,
    `${thumbnailUrl(template, 291.33, 163.86, 2, ext)} 582w`,
    `${thumbnailUrl(template, 343.00, 192.94, 1, ext)} 343w`,
    `${thumbnailUrl(template, 343.00, 192.94, 2, ext)} 686w`
  ].join(', ');
}

interface BattleCardProps {
  battle: Battle;
  fallbackImage: string;
  reltime: RelTimeTranslations;
}

export default function BattleCard (props: BattleCardProps) {
  const { battle, fallbackImage, reltime } = props;
  const [thumbnailLoaded, setThumbnailLoaded] = useState(false);

  return (
    <div className='col-xs-12 col-sm-6 col-md-4 col-lg-3 mb-2'>
      <div className={[classes.root, classes.outlined].join(' ')}>
        <a href={battle.url} className={classes.link}>
          <div
            className={[classes.media, classes.media16x9].join(' ')}
            style={{
              backgroundImage: buildBackgroundImages(battle, fallbackImage)
            } as CSSProperties}
          >
            {battle.thumbnail
              ? (
                <picture
                  className={[
                    classes.thumbnail,
                    thumbnailLoaded ? classes.thumbnailLoaded : null
                  ].filter(v => v !== null).join(' ')}
                >
                  <source
                    type='image/avif'
                    sizes={THUMBNAIL_SIZES}
                    srcSet={buildThumbnailSrcSet(battle.thumbnail, 'avif')}
                  />
                  <source
                    type='image/webp'
                    sizes={THUMBNAIL_SIZES}
                    srcSet={buildThumbnailSrcSet(battle.thumbnail, 'webp')}
                  />
                  <img
                    alt=''
                    decoding='async'
                    sizes={THUMBNAIL_SIZES}
                    srcSet={buildThumbnailSrcSet(battle.thumbnail, 'jpg')}
                    onLoad={() => setThumbnailLoaded(true)}
                  />
                </picture>
                )
              : null}
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

function buildBackgroundImages (battle: Battle, fallbackImage: string) {
  const results = [];
  if (battle.image && !battle.thumbnail) {
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

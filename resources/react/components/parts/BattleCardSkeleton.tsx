import classes from './BattleCardSkeleton.module.css';

export default function BattleCardSkeleton () {
  return (
    <div className='col-xs-12 col-sm-6 col-md-4 col-lg-3 mb-2' aria-hidden='true'>
      <div className={classes.root}>
        <div className={classes.media}>
          <div className={[classes.mediaInner, classes.shimmer].join(' ')} />
          <div className={classes.modeIcons}>
            <div className={[classes.modeIconsInner, classes.shimmer].join(' ')} />
          </div>
          <div className={classes.time}>
            <div className={[classes.timeInner, classes.shimmer].join(' ')} />
          </div>
        </div>
        <div className={classes.content}>
          <div className={[classes.userIcon, classes.shimmer].join(' ')} />
          <div className={classes.contentData}>
            <div className={[classes.line, classes.lineWide, classes.shimmer].join(' ')} />
            <div className={[classes.line, classes.lineMedium, classes.shimmer].join(' ')} />
            <div className={[classes.line, classes.lineNarrow, classes.shimmer].join(' ')} />
          </div>
        </div>
      </div>
    </div>
  );
}

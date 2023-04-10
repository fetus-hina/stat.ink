import PropTypes from 'prop-types';
import React from 'react';
import ScheduleContent from './ScheduleContent';
import esc from 'escape-html';
import { connect } from 'react-redux';
import { createUseStyles } from 'react-jss';

const useStyles = createUseStyles({
  tabContentRoot: {
    display: 'flex',
    flexWrap: 'nowrap',
    marginLeft: '-15px',
    marginRight: '-15px',

    '@media (max-width: 991px)': {
      flexWrap: 'wrap'
    }
  }
});

function ScheduleContents (props) {
  const { data, schedule, selected, translations } = props;
  const classes = useStyles();

  const mode = (() => {
    const list = data.filter(mode => mode.id === selected);
    return list.length === 1 ? list[0] : null;
  })();

  if (!mode) {
    return (
      <div id='schedule-content' className='tab-content'>
        <div className='tab-pane active' role='tabpanel' />
      </div>
    );
  }

  return (
    <div id='schedule-content' className='tab-content'>
      <div className='tab-pane active' role='tabpanel'>
        {(() => {
          const modeData = extractMode(schedule, mode);
          if (!modeData) {
            return null;
          }

          return (
            <>
              <div className={classes.tabContentRoot}>
                <ScheduleContent
                  mode={modeData.key}
                  modeIcon={modeData.image}
                  schedules={modeData.schedules}
                />
              </div>
              <p
                className='text-right mb-0'
                dangerouslySetInnerHTML={{
                  __html: getDataSourceHTML(schedule, modeData, translations)
                }}
              />
            </>
          );
        })()}
      </div>
    </div>
  );
}

ScheduleContents.propTypes = {
  data: PropTypes.array.isRequired,
  schedule: PropTypes.object,
  selected: PropTypes.string.isRequired,
  translations: PropTypes.object
};

function extractMode (schedule, mode) {
  if (schedule === null) {
    return null;
  }

  const ref = mode.ref.slice(); // ["splatoon2", "regular"]
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

function getDataSourceHTML (schedule, mode, translations) {
  if (
    !schedule ||
    !schedule.sources ||
    !mode ||
    !mode.source ||
    !schedule.sources[mode.source]
  ) {
    return '';
  }

  const source = schedule.sources[mode.source];
  const sourceHTML = `<a href="${esc(source.url)}" target="_blank">${esc(source.name)}</a>`;

  const template = (translations && translations.source)
    ? translations.source
    : 'Source: {source}';

  return esc(template).replace('{source}', sourceHTML);
}

function mapStateToProps (state) {
  return {
    now: Math.floor(state.schedule.currentTime / 1000),
    schedule: state.schedule.data,
    translations: state.schedule.data
      ? state.schedule.data.translations
      : null
  };
}

function mapDispatchToProps (/* dispatch */) {
  return {};
}

export default connect(mapStateToProps, mapDispatchToProps)(ScheduleContents);

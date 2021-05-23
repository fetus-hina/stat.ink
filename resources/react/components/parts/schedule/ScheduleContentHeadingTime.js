import PropTypes from 'prop-types';
import React from 'react';
import { DateTime } from 'luxon';
import { connect } from 'react-redux';

function ScheduleContentHeadingTime (props) {
  const { isSalmon, schedule } = props;

  const dtBegin = makeTimeStamp(props, schedule.time[0] * 1000);
  const dtEnd = makeTimeStamp(props, schedule.time[1] * 1000);

  return isSalmon
    ? salmonFormat(dtBegin, dtEnd)
    : normalFormat(dtBegin, dtEnd);
}

ScheduleContentHeadingTime.propTypes = {
  isSalmon: PropTypes.bool.isRequired,
  locale: PropTypes.object,
  schedule: PropTypes.object.isRequired
};

function normalFormat (dtBegin, dtEnd) {
  return (
    <span className='mr-1'>
      [{timeFormat(dtBegin, DateTime.TIME_SIMPLE)}-{timeFormat(dtEnd, DateTime.TIME_SIMPLE)}]
    </span>
  );
}

function salmonFormat (dtBegin, dtEnd) {
  return (
    <span className='mr-1'>
      [{timeFormat(dtBegin, DateTime.DATETIME_SHORT)}-{timeFormat(dtEnd, DateTime.DATETIME_SHORT)}]
    </span>
  );
}

function timeFormat (datetime, format) {
  return (
    <time dateTime={datetime.toISO()} title={datetime.toLocaleString(DateTime.DATETIME_MED)}>
      {datetime.toLocaleString(format)}
    </time>
  );
}

function makeTimeStamp (props, timestamp) {
  const { locale } = props;
  const localeOpts = {};
  if (locale) {
    localeOpts.zone = locale.timezone;
    localeOpts.locale = locale.locale;
    if (locale.calendar) {
      localeOpts.outputCalendar = locale.calendar;
    }
  }
  return DateTime.fromMillis(timestamp, localeOpts);
}

function mapStateToProps (state) {
  return {
    locale: state.schedule.data ? state.schedule.data.locale : null
  };
}

function mapDispatchToProps (/* dispatch */) {
  return {};
}

export default connect(mapStateToProps, mapDispatchToProps)(ScheduleContentHeadingTime);

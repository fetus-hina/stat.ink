import React from 'react';
import { DateTime } from 'luxon';
import { useSelector } from 'react-redux';

export default function ScheduleContentHeadingTime (props) {
  const { isSalmon, schedule } = props;
  const locale = useSelector(state => state.schedule.data ? state.schedule.data.locale : null);

  const dtBegin = makeTimeStamp(locale, schedule.time[0] * 1000);
  const dtEnd = makeTimeStamp(locale, schedule.time[1] * 1000);

  return isSalmon
    ? salmonFormat(dtBegin, dtEnd)
    : normalFormat(dtBegin, dtEnd);
}

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

function makeTimeStamp (locale, timestamp) {
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

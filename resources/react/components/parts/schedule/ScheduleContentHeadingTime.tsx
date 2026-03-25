import { DateTime } from 'luxon';
import { useSelector } from 'react-redux';
import type { IndexRootState } from '../../../store/indexApp';
import type { ScheduleEntry, ScheduleLocale } from '../../../types';

interface ScheduleContentHeadingTimeProps {
  isSalmon: boolean;
  schedule: ScheduleEntry;
}

export default function ScheduleContentHeadingTime (props: ScheduleContentHeadingTimeProps) {
  const { isSalmon, schedule } = props;
  const locale = useSelector((state: IndexRootState) => state.schedule.data ? state.schedule.data.locale : null);

  const dtBegin = makeTimeStamp(locale, schedule.time[0] * 1000);
  const dtEnd = makeTimeStamp(locale, schedule.time[1] * 1000);

  return isSalmon
    ? salmonFormat(dtBegin, dtEnd)
    : normalFormat(dtBegin, dtEnd);
}

function normalFormat (dtBegin: DateTime, dtEnd: DateTime) {
  return (
    <span className='mr-1'>
      [{timeFormat(dtBegin, DateTime.TIME_SIMPLE)}-{timeFormat(dtEnd, DateTime.TIME_SIMPLE)}]
    </span>
  );
}

function salmonFormat (dtBegin: DateTime, dtEnd: DateTime) {
  return (
    <span className='mr-1'>
      [{timeFormat(dtBegin, DateTime.DATETIME_SHORT)}-{timeFormat(dtEnd, DateTime.DATETIME_SHORT)}]
    </span>
  );
}

function timeFormat (datetime: DateTime, format: Intl.DateTimeFormatOptions) {
  return (
    <time dateTime={datetime.toISO() ?? undefined} title={datetime.toLocaleString(DateTime.DATETIME_MED)}>
      {datetime.toLocaleString(format)}
    </time>
  );
}

function makeTimeStamp (locale: ScheduleLocale | null, timestamp: number) {
  const localeOpts: { zone?: string; locale?: string; outputCalendar?: string } = {};
  if (locale) {
    localeOpts.zone = locale.timezone;
    localeOpts.locale = locale.locale;
    if (locale.calendar) {
      localeOpts.outputCalendar = locale.calendar;
    }
  }
  return DateTime.fromMillis(timestamp, localeOpts);
}

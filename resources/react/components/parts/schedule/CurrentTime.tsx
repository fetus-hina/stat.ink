import { DateTime } from 'luxon';
import { useSelector } from 'react-redux';

export default function CurrentTime () {
  const locale = useSelector((state: any) =>
    (state.schedule.data && state.schedule.data.locale)
      ? state.schedule.data.locale
      : null
  );
  const translations = useSelector((state: any) =>
    (state.schedule.data && state.schedule.data.translations)
      ? state.schedule.data.translations
      : null
  );
  const currentTime = useSelector((state: any) => state.schedule.currentTime);

  if (!translations) {
    return null;
  }

  const localeOpts: any = {};
  if (locale) {
    localeOpts.zone = locale.timezone;
    localeOpts.locale = locale.locale;
    if (locale.calendar) {
      localeOpts.outputCalendar = locale.calendar;
    }
  }

  const now = DateTime.fromMillis(currentTime, localeOpts);

  const time = (
    <time dateTime={now.toISO() ?? undefined}>
      {now.toLocaleString(DateTime.DATETIME_SHORT) + ' '}
      <a href='#timezone-dialog' data-toggle='modal'>
        {now.offsetNameShort}
      </a>
    </time>
  );

  return (
    <span className='small ml-2'>
      [{translations.current_time} {time}]
    </span>
  );
}

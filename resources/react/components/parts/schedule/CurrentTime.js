import PropTypes from 'prop-types';
import React from 'react';
import { DateTime } from 'luxon';
import { connect } from 'react-redux';

function CurrentTime(props) {
  const { now, translations } = props;

  if (!translations) {
    return null;
  }

  const time = (
    <time dateTime={now.toISO()}>
      {now.toLocaleString(DateTime.DATETIME_SHORT) + ' '}
      <a href="#timezone-dialog" data-toggle="modal">
        {now.offsetNameShort}
      </a>
    </time>
  );

  return (
    <span className="small ml-2">
      [{translations.current_time} {time}]
    </span>
  );
}

CurrentTime.propTypes = {
  now: PropTypes.object.isRequired,
  translations: PropTypes.object,
};

function mapStateToProps(state) {
  const locale = (state.schedule.data && state.schedule.data.locale)
    ? state.schedule.data.locale
    : null;

  const localeOpts = {};
  if (locale) {
    localeOpts.zone = locale.timezone;
    localeOpts.locale = locale.locale;
    if (locale.calendar) {
      localeOpts.outputCalendar = locale.calendar;
    }
  }

  return {
    now: DateTime.fromMillis(state.schedule.currentTime, localeOpts),
    translations: (state.schedule.data && state.schedule.data.translations)
      ? state.schedule.data.translations
      : null,
  };
}

function mapDispatchToProps(/* dispatch */) {
  return {};
}

export default connect(mapStateToProps, mapDispatchToProps)(CurrentTime);

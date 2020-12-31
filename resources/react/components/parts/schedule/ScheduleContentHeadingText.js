// import React from 'react';
import PropTypes from 'prop-types';

export default function ScheduleContentHeadingText(props) {
  const { schedule } = props;

  if (!schedule || !schedule.rule) {
    return null;
  }

  return schedule.rule.name;
}

ScheduleContentHeadingText.propTypes = {
  schedule: PropTypes.object.isRequired,
};

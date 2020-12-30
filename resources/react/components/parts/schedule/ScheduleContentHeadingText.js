import PropTypes from 'prop-types';
import React from 'react';

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

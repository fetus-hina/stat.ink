import PropTypes from 'prop-types';
import React from 'react';
import ScheduleTab from './ScheduleTab';

export default function ScheduleTabs (props) {
  const { data, onChanged, selected } = props;

  return (
    <nav>
      <ul className='nav nav-tabs' role='tablist'>
        {data.map(tab => (
          <ScheduleTab
            isSelected={tab.id === selected}
            item={tab}
            key={tab.id}
            onChanged={onChanged}
          />
        ))}
      </ul>
    </nav>
  );
}

ScheduleTabs.propTypes = {
  data: PropTypes.array.isRequired,
  onChanged: PropTypes.func.isRequired,
  selected: PropTypes.string.isRequired
};

import Heading from './Heading';
import React from 'react';
import ScheduleDisplay from './ScheduleDisplay';

export default function Schedule () {
  return (
    <aside className='mb-3'>
      <Heading />
      <ScheduleDisplay />
    </aside>
  );
}

Schedule.propTypes = {
};

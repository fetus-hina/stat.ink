import React, { useState } from 'react';
import ScheduleContents from './ScheduleContents';
import ScheduleTabs from './ScheduleTabs';

const data = [
  {
    id: 'regular3',
    ref: 'splatoon3/regular'.split('/'),
    label: 'Regular'
  },
  {
    id: 'challenge3',
    ref: 'splatoon3/bankara_challenge'.split('/'),
    label: 'Anarchy (Series)'
  },
  {
    id: 'open3',
    ref: 'splatoon3/bankara_open'.split('/'),
    label: 'Anarchy (Open)'
  },
  {
    id: 'regular2',
    ref: 'splatoon2/regular'.split('/'),
    label: 'Regular'
  },
  {
    id: 'gachi2',
    ref: 'splatoon2/gachi'.split('/'),
    label: 'Ranked'
  },
  {
    id: 'league2',
    ref: 'splatoon2/league'.split('/'),
    label: 'League'
  },
  {
    id: 'salmon2',
    ref: 'splatoon2/salmon'.split('/'),
    label: 'Salmon Run'
  }
];

export default function ScheduleDisplay () {
  const [selected, setSelected] = useState('challenge3');

  return (
    <>
      <ScheduleTabs
        data={data}
        onChanged={(id) => setSelected(id)}
        selected={selected}
      />
      <ScheduleContents
        data={data}
        selected={selected}
      />
    </>
  );
}

ScheduleDisplay.propTypes = {
};

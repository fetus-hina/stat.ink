import React, { useState } from 'react';
import ScheduleContents from './ScheduleContents';
import ScheduleTabs from './ScheduleTabs';

const data = [
  {
    id: 'regular3',
    ref: 'splatoon3/regular'.split('/'),
    label: 'Regular',
    showOpen: false
  },
  {
    id: 'challenge3',
    ref: 'splatoon3/bankara_challenge'.split('/'),
    label: 'Anarchy (Series)',
    showOpen: false
  },
  {
    id: 'open3',
    ref: 'splatoon3/bankara_open'.split('/'),
    label: 'Anarchy (Open)',
    showOpen: false
  },
  {
    id: 'xmatch3',
    ref: 'splatoon3/xmatch'.split('/'),
    label: 'X Battle',
    showOpen: false
  },
  {
    id: 'salmon3',
    ref: 'splatoon3/salmon'.split('/'),
    label: 'Salmon Run',
    showOpen: false
  },
  {
    id: 'regular2',
    ref: 'splatoon2/regular'.split('/'),
    label: 'Regular',
    showOpen: false
  },
  {
    id: 'gachi2',
    ref: 'splatoon2/gachi'.split('/'),
    label: 'Ranked',
    showOpen: false
  },
  {
    id: 'league2',
    ref: 'splatoon2/league'.split('/'),
    label: 'League',
    showOpen: false
  },
  {
    id: 'salmon2',
    ref: 'splatoon2/salmon'.split('/'),
    label: 'Salmon Run',
    showOpen: true
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

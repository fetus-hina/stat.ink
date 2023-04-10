import PropTypes from 'prop-types';
import React, { useState } from 'react';
import ScheduleContents from './ScheduleContents';
import ScheduleTabs from './ScheduleTabs';
import { connect } from 'react-redux';

const data = [
  {
    id: 'regular3',
    ref: 'splatoon3/regular'.split('/'),
    label: 'Regular',
    showOpen: false,
    priority: 4
  },
  {
    id: 'challenge3',
    ref: 'splatoon3/bankara_challenge'.split('/'),
    label: 'Anarchy (Series)',
    showOpen: false,
    priority: 1
  },
  {
    id: 'open3',
    ref: 'splatoon3/bankara_open'.split('/'),
    label: 'Anarchy (Open)',
    showOpen: false,
    priority: 2
  },
  {
    id: 'xmatch3',
    ref: 'splatoon3/xmatch'.split('/'),
    label: 'X Battle',
    showOpen: false,
    priority: 3
  },
  {
    id: 'splatfest3',
    ref: 'splatoon3/splatfest_open'.split('/'),
    label: 'Splatfest',
    showOpen: false,
    priority: 5
  },
  {
    id: 'salmon3',
    ref: 'splatoon3/salmon'.split('/'),
    label: 'Salmon Run',
    showOpen: false,
    priority: 6
  },
  {
    id: 'eggstra3',
    ref: 'splatoon3/salmon_eggstra'.split('/'),
    label: 'Eggstra Work',
    showOpen: true,
    priority: 7
  },
  {
    id: 'regular2',
    ref: 'splatoon2/regular'.split('/'),
    label: 'Regular',
    showOpen: false,
    priority: 12
  },
  {
    id: 'gachi2',
    ref: 'splatoon2/gachi'.split('/'),
    label: 'Ranked',
    showOpen: false,
    priority: 11
  },
  {
    id: 'league2',
    ref: 'splatoon2/league'.split('/'),
    label: 'League',
    showOpen: false,
    priority: 13
  },
  {
    id: 'salmon2',
    ref: 'splatoon2/salmon'.split('/'),
    label: 'Salmon Run',
    showOpen: true,
    priority: 14
  }
];

function ScheduleDisplay (props) {
  const { schedule } = props;
  let [selected, setSelected] = useState('AUTO');

  // ユーザーによるスケジュールタブ選択がされていないとき、
  // 可能であれば存在するデータのうち priority が最も小さいものを
  // 選択したものとみなして表示する
  if (selected === 'AUTO' && data && schedule) {
    let currentPriority = 0x7fffffff;
    data.forEach(tab => {
      const mode = extractMode(schedule, tab);
      if (
        mode &&
        mode.schedules &&
        mode.schedules.length > 0 &&
        currentPriority > tab.priority
      ) {
        selected = tab.id;
        currentPriority = tab.priority;
      }
    });
  }

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
  schedule: PropTypes.object
};

function extractMode (schedule, tabItem) {
  const ref = tabItem.ref.slice(); // ["splatoon2", "regular"]
  let current = Object.assign({}, schedule);
  while (current && ref.length > 0) {
    const curRef = ref.shift();
    if (!current[curRef]) {
      return null;
    }
    current = current[curRef];
  }
  return current;
}

function mapStateToProps (state) {
  return {
    schedule: state.schedule.data
  };
}

function mapDispatchToProps (/* dispatch */) {
  return {};
}

export default connect(mapStateToProps, mapDispatchToProps)(ScheduleDisplay);

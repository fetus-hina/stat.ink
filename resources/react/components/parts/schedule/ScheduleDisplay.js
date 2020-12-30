import PropTypes from 'prop-types';
import React, { useState } from 'react';
import ScheduleContents from './ScheduleContents';
import ScheduleTabs from './ScheduleTabs';
import { connect } from 'react-redux';

const data = [
  {
    id: 'regular2',
    ref: 'splatoon2/regular'.split('/'),
    label: 'Regular',
  },
  {
    id: 'gachi2',
    ref: 'splatoon2/gachi'.split('/'),
    label: 'Ranked',
  },
  {
    id: 'league2',
    ref: 'splatoon2/league'.split('/'),
    label: 'League',
  },
  {
    id: 'salmon2',
    ref: 'splatoon2/salmon'.split('/'),
    label: 'Salmon Run',
  },
];

function ScheduleDisplay() {
  const [ selected, setSelected ] = useState('regular2');

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

function mapStateToProps(/* state */) {
  return {};
}

function mapDispatchToProps(/* dispatch */) {
  return {};
}

export default connect(mapStateToProps, mapDispatchToProps)(ScheduleDisplay);

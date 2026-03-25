import React from 'react';

export default function CounterIcon (props) {
  const { icon } = props;

  return icon
    ? <img
        alt=''
        className='basic-icon'
        draggable='false'
        src={icon}
        style={{
          '--icon-height': '1em',
          '--icon-valign': 'middle'
        }}
        title=''
      />
    : null;
}

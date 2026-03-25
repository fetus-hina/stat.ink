import type { CSSProperties } from 'react';

interface CounterIconProps {
  icon: string | null;
}

export default function CounterIcon (props: CounterIconProps) {
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
        } as CSSProperties}
        title=''
      />
    : null;
}

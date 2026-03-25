import ScheduleTab from './ScheduleTab';
import type { TabItem } from '../../../types';

interface ScheduleTabsProps {
  data: TabItem[];
  onChanged: (id: string) => void;
  selected: string;
}

export default function ScheduleTabs (props: ScheduleTabsProps) {
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

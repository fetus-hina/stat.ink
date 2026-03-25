import CounterItem from './CounterItem';
import classes from './Counter.module.css';
import { STATUS_OK } from '../../../constants';
import { useSelector } from 'react-redux';
import type { CounterRootState } from '../../../store/counterApp';
import type { CounterData, CounterEntry } from '../../../types';

const DEFAULT_DIGITS = 8;

const isEmptyObject = (obj: CounterData) => Object.keys(obj).length === 0;

export default function Counter () {
  const data = useSelector((state: CounterRootState) => state.counter.data);
  const status = useSelector((state: CounterRootState) => state.counter.status);

  const rows = createRows(data);
  const maxDigit = rows
    .map(v => v.count !== null ? String(v.count).length : DEFAULT_DIGITS)
    .reduce((acc, cur) => Math.max(acc, cur), DEFAULT_DIGITS);

  return (
    <aside
      className={
        [
          classes.root,
          status !== STATUS_OK && isEmptyObject(data) ? classes.initializing : null
        ]
          .filter(v => v !== null)
          .join(' ')
      }
    >
      {rows.map(v => (
        <CounterItem
          key={v.type}
          digit={maxDigit}
          icon={v.icon}
          label={v.label}
          popup={v.popup}
          value={v.count}
        />
      ))}
    </aside>
  );
}

const numberFormat = (number: number) => Number(number)
  .toLocaleString(window?.document?.documentElement?.lang ?? 'en-US');

interface CounterRow {
  type: string;
  icon: string | null;
  label: string;
  popup: string | null;
  count: number | null;
}

function createRows (jsonData: CounterData) {
  const results: CounterRow[] = [
    {
      type: 'user',
      icon: null,
      label: 'Users',
      popup: null,
      count: null
    },
    {
      type: 'battle',
      icon: null,
      label: 'Battles',
      popup: null,
      count: null
    },
    {
      type: 'salmon',
      icon: null,
      label: 'Shifts',
      popup: null,
      count: null
    }
  ];

  results.forEach(currentRow => {
    const typeMatchedJsonItems: [string, CounterEntry][] = Object.entries(jsonData)
      .filter(([, v]) => (v.type === currentRow.type))
      .sort(([a], [b]) => a.localeCompare(b));
    if (typeMatchedJsonItems.length > 0) {
      currentRow.icon = typeMatchedJsonItems[0][1].icon;
      currentRow.label = typeMatchedJsonItems[0][1].label;
      currentRow.count = typeMatchedJsonItems
        .map(([, v]) => Number(v.count))
        .reduce((acc, cur) => acc + cur, 0);
      currentRow.popup = typeMatchedJsonItems.length > 1
        ? typeMatchedJsonItems
          .map(([, v]) => numberFormat(v.count))
          .join(' + ')
        : null;
    }
  });

  return results;
}

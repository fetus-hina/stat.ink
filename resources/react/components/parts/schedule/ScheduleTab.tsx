import classes from './ScheduleTab.module.css';
import { useSelector } from 'react-redux';
import type { IndexRootState } from '../../../store/indexApp';
import type { ScheduleData, ScheduleEntry, ScheduleGameIcon, ScheduleMode, ScheduleTranslations, TabItem } from '../../../types';

interface ScheduleTabProps {
  isSelected: boolean;
  item: TabItem;
  onChanged: (id: string) => void;
}

export default function ScheduleTab (props: ScheduleTabProps) {
  const { isSelected, item, onChanged } = props;
  const gameIcons = useSelector((state: IndexRootState) => state.schedule.data ? state.schedule.data.games : null);
  const now = useSelector((state: IndexRootState) => Math.floor(state.schedule.currentTime / 1000));
  const schedule = useSelector((state: IndexRootState) => state.schedule.data);
  const translations = useSelector((state: IndexRootState) => state.schedule.data ? state.schedule.data.translations : null);

  const mode = extractMode(schedule, item);

  if (!mode || !mode.schedules || mode.schedules.length < 1) {
    return null;
  }

  return (
    <li
      className={isSelected ? 'active' : undefined}
      role='tab'
      aria-controls='schedule-content'
      aria-selected={isSelected}
    >
      <a className={classes.pointer} onClick={() => { onChanged(item.id); }}>
        {label(mode, { gameIcons, isSelected, item, now, translations })}
      </a>
    </li>
  );
}

interface LabelProps {
  gameIcons: Record<string, ScheduleGameIcon> | null;
  isSelected: boolean;
  item: TabItem;
  now: number;
  translations: ScheduleTranslations | null;
}

function label (mode: ScheduleMode, props: LabelProps) {
  const { gameIcons, isSelected, item, now, translations } = props;

  if (!mode) {
    return item.label;
  }

  const current = extractCurrent(mode, now);

  return (
    <>
      {mode.game && gameIcons && gameIcons[mode.game] && gameIcons[mode.game].icon
        ? (
          <>
            <img
              alt={gameIcons[mode.game].name}
              className={classes.modeIcon}
              src={gameIcons[mode.game].icon ?? undefined}
              title={gameIcons[mode.game].name}
            />
            {' '}
          </>
          )
        : null}
      {mode.image
        ? (
          <>
            <img
              alt=''
              className={classes.modeIcon}
              src={mode.image}
              title={mode.name}
            />
            {' '}
          </>
          )
        : null}
      {current?.rule?.icon
        ? (
          <>
            <img
              alt={current.rule.name}
              className={classes.modeIcon}
              src={current.rule.icon}
              title={current.rule.name}
            />
            {' '}
          </>
          )
        : null}
      {current?.king?.image
        ? (
          <>
            <img
              alt={current.king.name}
              className={classes.modeIcon}
              src={current.king.image}
              title={current.king.name}
            />
            {' '}
          </>
          )
        : null}
      {current?.weapons && item.showOpen
        ? (
          <>
            <span className='text-warning' title={translations ? translations.salmon_open : 'Open!'}>
              <span className='fas fa-fw fa-certificate' />
            </span>
            {' '}
          </>
          )
        : null}
      {isSelected ? mode.name : null}
    </>
  );
}

function extractMode (schedule: ScheduleData | null, tabItem: TabItem): ScheduleMode | null {
  if (!schedule) {
    return null;
  }

  const ref = tabItem.ref.slice();
  let current: Record<string, unknown> = Object.assign({}, schedule);
  while (current && ref.length > 0) {
    const curRef = ref.shift()!;
    if (!current[curRef]) {
      return null;
    }
    current = current[curRef] as Record<string, unknown>;
  }
  return current as unknown as ScheduleMode;
}

function extractCurrent (mode: ScheduleMode, now: number): ScheduleEntry | null {
  if (!mode || !mode.schedules) {
    return null;
  }

  const matches = mode.schedules.filter((item: ScheduleEntry) => {
    return item.time[0] <= now && now < item.time[1];
  });
  if (matches.length !== 1) {
    return null;
  }

  const current = matches.shift();
  return current ?? null;
}

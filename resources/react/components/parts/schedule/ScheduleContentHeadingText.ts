import type { ScheduleEntry } from '../../../types';

interface ScheduleContentHeadingTextProps {
  schedule: ScheduleEntry;
  isSalmon?: boolean;
}

export default function ScheduleContentHeadingText (props: ScheduleContentHeadingTextProps) {
  const { schedule } = props;

  if (!schedule || !schedule.rule) {
    return null;
  }

  return schedule.rule.name;
}

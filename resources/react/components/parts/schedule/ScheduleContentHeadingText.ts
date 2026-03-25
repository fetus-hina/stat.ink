interface ScheduleContentHeadingTextProps {
  schedule: any;
  isSalmon?: boolean;
}

export default function ScheduleContentHeadingText (props: ScheduleContentHeadingTextProps) {
  const { schedule } = props;

  if (!schedule || !schedule.rule) {
    return null;
  }

  return schedule.rule.name;
}

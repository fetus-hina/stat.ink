
interface ScheduleContentHeadingIconProps {
  schedule: any;
  isSalmon?: boolean;
}

export default function ScheduleContentHeadingIcon (props: ScheduleContentHeadingIconProps) {
  const { schedule } = props;

  if (!schedule || !schedule.rule || !schedule.rule.icon) {
    return null;
  }

  return (
    <img src={schedule.rule.icon} className='mr-1' width='24' height='24' />
  );
}

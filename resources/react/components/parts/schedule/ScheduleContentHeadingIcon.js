
export default function ScheduleContentHeadingIcon (props) {
  const { schedule } = props;

  if (!schedule || !schedule.rule || !schedule.rule.icon) {
    return null;
  }

  return (
    <img src={schedule.rule.icon} className='mr-1' width='24' height='24' />
  );
}

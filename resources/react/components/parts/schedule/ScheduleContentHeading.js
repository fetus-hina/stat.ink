import ScheduleContentHeadingIcon from './ScheduleContentHeadingIcon';
import ScheduleContentHeadingText from './ScheduleContentHeadingText';
import ScheduleContentHeadingTime from './ScheduleContentHeadingTime';

export default function ScheduleContentHeading (props) {
  const { mode, schedule } = props;
  const isSalmon = String(mode).startsWith('salmon');

  return (
    <h3>
      <ScheduleContentHeadingTime schedule={schedule} isSalmon={isSalmon} />
      <ScheduleContentHeadingIcon schedule={schedule} isSalmon={isSalmon} />
      <ScheduleContentHeadingText schedule={schedule} isSalmon={isSalmon} />
    </h3>
  );
}

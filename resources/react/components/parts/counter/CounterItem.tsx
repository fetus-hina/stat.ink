import CounterIcon from './CounterIcon';
import CounterDisplay from './CounterDisplay';
import classes from './CounterItem.module.css';

interface CounterItemProps {
  digit: number;
  icon: string | null;
  label: string;
  popup: string | null;
  value: number | null;
}

export default function CounterItem (props: CounterItemProps) {
  const { digit, icon, label, popup, value } = props;

  return (
    <div className={classes.item}>
      <span className={classes.icon}>
        <CounterIcon icon={icon} />
      </span>
      <span className={classes.label}>
        {label}:
      </span>
      <span className={classes.value} title={popup ?? undefined}>
        <CounterDisplay
          digit={digit}
          value={value}
        />
      </span>
    </div>
  );
}

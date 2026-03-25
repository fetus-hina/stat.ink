import CounterIcon from './CounterIcon';
import CounterDisplay from './CounterDisplay';
import classes from './CounterItem.module.css';

export default function CounterItem (props) {
  const { digit, icon, label, popup, value } = props;

  return (
    <div className={classes.item}>
      <span className={classes.icon}>
        <CounterIcon icon={icon} />
      </span>
      <span className={classes.label}>
        {label}:
      </span>
      <span className={classes.value} title={popup}>
        <CounterDisplay
          digit={digit}
          value={value}
        />
      </span>
    </div>
  );
}

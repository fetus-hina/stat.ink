import classes from './CounterDisplay.module.css';

interface CounterDisplayProps {
  value: number | null;
  digit: number;
}

export default function CounterDisplay (props: CounterDisplayProps) {
  const { value, digit } = props;

  const strValue = value !== null ? String(value) : '-'.repeat(digit);
  const padding = (strValue.length < digit)
    ? (
      <span className={classes.padding} aria-hidden='true'>
        {'!'.repeat(digit - strValue.length)}
      </span>
      )
    : null;

  return (
    <span className={classes.root}>
      <span className={classes.bgNumber} aria-hidden='true'>
        {'8'.repeat(digit)}
      </span>
      <span>
        {padding}{strValue}
      </span>
    </span>
  );
}

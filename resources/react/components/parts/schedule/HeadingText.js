import { useSelector } from 'react-redux';

export default function HeadingText () {
  const translations = useSelector(state => state.schedule.data ? state.schedule.data.translations : null);
  return translations ? translations.heading : 'Schedule';
}

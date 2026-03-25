import { useSelector } from 'react-redux';
import type { IndexRootState } from '../../../store/indexApp';

export default function HeadingText () {
  const translations = useSelector((state: IndexRootState) => state.schedule.data ? state.schedule.data.translations : null);
  return translations ? translations.heading : 'Schedule';
}

import { STATUS_LOADING } from '../../../constants';
import { useSelector } from 'react-redux';
import type { IndexRootState } from '../../../store/indexApp';

export default function Loading () {
  const isLoading = useSelector((state: IndexRootState) => state.schedule.status === STATUS_LOADING);

  if (!isLoading) {
    return null;
  }

  return (
    <span className='fas fa-spinner fa-pulse' />
  );
}

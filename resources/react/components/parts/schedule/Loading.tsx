import { STATUS_LOADING } from '../../../constants';
import { useSelector } from 'react-redux';

export default function Loading () {
  const isLoading = useSelector((state: any) => state.schedule.status === STATUS_LOADING);

  if (!isLoading) {
    return null;
  }

  return (
    <span className='fas fa-spinner fa-pulse' />
  );
}

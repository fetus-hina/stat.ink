import Impl from './blog/BlogEntries';
import { useEffect, useRef } from 'react';
import { useSelector, useDispatch } from 'react-redux';
import { fetchBlogEntry } from '../../actions/blog';
import type { IndexRootState, IndexAppDispatch } from '../../store/indexApp';

export default function Blog () {
  const dispatch = useDispatch<IndexAppDispatch>();
  const expires = useSelector((state: IndexRootState) => state.blog.expires);
  const expiresRef = useRef(expires);
  expiresRef.current = expires;

  useEffect(() => {
    dispatch(fetchBlogEntry());
    const timer = window.setInterval(() => {
      if (expiresRef.current > (new Date()).getTime()) {
        return;
      }
      dispatch(fetchBlogEntry());
    }, 500);
    return () => { window.clearInterval(timer); };
  }, [dispatch]);

  return <Impl />;
}

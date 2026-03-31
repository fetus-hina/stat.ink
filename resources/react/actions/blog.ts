import { Dispatch } from '@reduxjs/toolkit';
import { BlogEntry } from '../types';

export const FETCH_BLOG_ENTRY = 'FETCH_BLOG_ENTRY';
export const FETCH_BLOG_ENTRY_FAILED = 'FETCH_BLOG_ENTRY_FAILED';
export const FETCH_BLOG_ENTRY_SUCCESS = 'FETCH_BLOG_ENTRY_SUCCESS';

export function fetchBlogEntryFailed (error: unknown) {
  return {
    type: FETCH_BLOG_ENTRY_FAILED,
    value: error
  };
}

export function fetchBlogEntrySuccess (data: BlogEntry[]) {
  return {
    type: FETCH_BLOG_ENTRY_SUCCESS,
    value: data
  };
}

export function fetchBlogEntry () {
  return (dispatch: Dispatch) => {
    dispatch({ type: FETCH_BLOG_ENTRY });
    return fetch('/api/internal/blog-entry')
      .then(response => response.json())
      .then(data => {
        dispatch(fetchBlogEntrySuccess(data));
      })
      .catch(error => {
        dispatch(fetchBlogEntryFailed(error));
      });
  };
}

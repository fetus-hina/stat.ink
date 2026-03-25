import axios from 'axios';

export const FETCH_BLOG_ENTRY = 'FETCH_BLOG_ENTRY';
export const FETCH_BLOG_ENTRY_FAILED = 'FETCH_BLOG_ENTRY_FAILED';
export const FETCH_BLOG_ENTRY_SUCCESS = 'FETCH_BLOG_ENTRY_SUCCESS';

export function fetchBlogEntryFailed (error) {
  return {
    type: FETCH_BLOG_ENTRY_FAILED,
    value: error
  };
}

export function fetchBlogEntrySuccess (data) {
  return {
    type: FETCH_BLOG_ENTRY_SUCCESS,
    value: data
  };
}

export function fetchBlogEntry () {
  return (dispatch) => {
    dispatch({ type: FETCH_BLOG_ENTRY });
    return axios
      .get('/api/internal/blog-entry')
      .then(response => {
        dispatch(fetchBlogEntrySuccess(response.data));
      })
      .catch(error => {
        dispatch(fetchBlogEntryFailed(error));
      });
  };
}

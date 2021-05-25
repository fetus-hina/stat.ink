export const FETCH_BLOG_ENTRY = 'FETCH_BLOG_ENTRY';
export const FETCH_BLOG_ENTRY_FAILED = 'FETCH_BLOG_ENTRY_FAILED';
export const FETCH_BLOG_ENTRY_SUCCESS = 'FETCH_BLOG_ENTRY_SUCCESS';

export function fetchBlogEntry () {
  return {
    type: FETCH_BLOG_ENTRY
  };
}

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

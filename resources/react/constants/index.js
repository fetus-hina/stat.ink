const TIMESTAMP_MILLISEC_TO_SEC = 1000;

export const BLOG_ENTRIES_LIFETIME = 10 * 60 * TIMESTAMP_MILLISEC_TO_SEC; // 10 mins
export const SCHEDULE_MAX_LIFETIME = 3600 * TIMESTAMP_MILLISEC_TO_SEC; // 1 hour

export const EXPIRED_TIMESTAMP = 0;

export const STATUS_EXPIRED = 'expired';
export const STATUS_FAILED = 'failed';
export const STATUS_LOADING = 'loading';
export const STATUS_OK = 'ok';

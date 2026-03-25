import React from 'react';
import classes from './BlogEntries.module.css';
import { STATUS_FAILED, STATUS_LOADING } from '../../../constants';
import { useSelector } from 'react-redux';

export default function BlogEntries () {
  const data = useSelector(state => state.blog.data);
  const status = useSelector(state => state.blog.status);

  return (
    <aside>
      {renderAlert({ data, status })}
    </aside>
  );
}

function renderAlert (props) {
  const { status, data } = props;

  if (status === STATUS_FAILED && !data.length) {
    return (
      <div className='alert alert-error' role='alert'>
        Failed to load blog entries
      </div>
    );
  } else {
    return (
      <div className='alert alert-success blog-entries' role='alert'>
        <nav>
          <ul className={classes.ul}>
            {renderStatus(props)}
            {renderEntries(props)}
          </ul>
        </nav>
      </div>
    );
  }
}

function renderStatus (props) {
  const { status } = props;

  switch (status) {
    case STATUS_FAILED:
      return <li className={classes.li}><span className='fas fa-exclamation-triangle' /></li>;
    case STATUS_LOADING:
      return <li className={classes.li}><span className='fas fa-spinner fa-pulse' /></li>;
    default:
      return <></>;
  }
}

function renderEntries (props) {
  const { data } = props;
  return (
    <>
      {data.map(entry => (
        <li key={entry.id} className={classes.li}>
          <a
            className={['alert-link', classes.alertLink].join(' ')}
            href={entry.url}
            rel='noreferrer'
            target='_blank'
          >
            {entry.title} ({renderTime(entry.at)})
          </a>
        </li>
      ))}
    </>
  );
}

function renderTime (at) {
  return (
    <time title={at.natural} dateTime={at.iso8601}>
      {at.relative}
    </time>
  );
}

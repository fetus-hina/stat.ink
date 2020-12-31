import PropTypes from 'prop-types';
import React from 'react';
import { STATUS_FAILED, STATUS_LOADING } from '../../../constants';
import { connect } from 'react-redux';
import { createUseStyles } from 'react-jss';

const useStyles = createUseStyles({
  ul: {
    listStyleType: 'none',
    listStyleImage: 'none',
    margin: '0',
    padding: '0',
    display: 'block',
  },
  li: {
    listStyleType: 'none',
    listStyleImage: 'none',
    margin: '0 1em 0 0',
    padding: '0',
    display: 'inline-block',

    '&::after': {
      display: 'inline',
      content: '"|"',
      marginLeft: '1em',
    },

    '&:last-child::after': {
      display: 'none',
    }
  },
  alertLink: {
    'fontWeight': 'normal !important',
  },
});

function BlogEntries(props) {
  return (
    <aside>
      {renderAlert(props)}
    </aside>
  );
}

function renderAlert(props) {
  const { status, data } = props;
  const classes = useStyles();

  if (status === STATUS_FAILED && !data.length) {
    return (
      <div className="alert alert-error" role="alert">
        Failed to load blog entries
      </div>
    );
  } else {
    return (
      <div className="alert alert-success blog-entries" role="alert">
        <nav>
          <ul className={classes.ul}>
            {renderStatus(props, classes)}
            {renderEntries(props, classes)}
          </ul>
        </nav>
      </div>
    );
  }
}

function renderStatus(props, classes) {
  const { status } = props;

  switch (status) {
    case STATUS_FAILED:
      return <li className={classes.li}><span className="fas fa-exclamation-triangle" /></li>;
    case STATUS_LOADING:
      return <li className={classes.li}><span className="fas fa-spinner fa-pulse" /></li>;
    default:
      return <></>;
  }
}

function renderEntries(props, classes) {
  const { data } = props;
  return (
    <>
      {data.map(entry => (
        <li key={entry.id} className={classes.li}>
          <a
            className={['alert-link', classes.alertLink].join(' ')}
            href={entry.url}
            rel="noreferrer"
            target="_blank"
          >
            {entry.title} ({renderTime(entry.at)})
          </a>
        </li>
      ))}
    </>
  );
}

function renderTime(at) {
  return (
    <time title={at.natural} dateTime={at.iso8601}>
      {at.relative}
    </time>
  );
}

BlogEntries.propTypes = {
  data: PropTypes.array.isRequired,
  status: PropTypes.string.isRequired,
};

function mapStateToProps(state) {
  return {
    data: state.blog.data,
    status: state.blog.status,
  };
}

function mapDispatchToProps(/* dispatch */) {
  return {};
}

export default connect(mapStateToProps, mapDispatchToProps)(BlogEntries);

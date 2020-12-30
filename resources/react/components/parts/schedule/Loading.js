import PropTypes from 'prop-types';
import React from 'react';
import { STATUS_LOADING } from '../../../constants';
import { connect } from 'react-redux';

function Loading(props) {
  const { isLoading } = props;

  if (!isLoading) {
    return null;
  }

  return (
    <span className="fas fa-spinner fa-pulse" />
  );
}

Loading.propTypes = {
  isLoading: PropTypes.bool.isRequired,
};

function mapStateToProps(state) {
  return {
    isLoading: state.schedule.status === STATUS_LOADING,
  };
}

function mapDispatchToProps(/* dispatch */) {
  return {};
}

export default connect(mapStateToProps, mapDispatchToProps)(Loading);

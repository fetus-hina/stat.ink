import Impl from './counter/Counter';
import PropTypes from 'prop-types';
import React, { Component } from 'react';
import { connect } from 'react-redux';
import { fetchCounter } from '../../actions/counter';

class Counter extends Component {
  constructor(...args) {
    super(...args);
    this.state = {
      timer: null,
    };
  }

  componentDidMount() {
    this.props.onMount(this);
  }

  componentWillUnmount() {
    this.props.onUnmount(this);
  }

  render() {
    return <Impl />;
  }
}

Counter.propTypes = {
  expires: PropTypes.number.isRequired,
  onMount: PropTypes.func.isRequired,
  onTickTimer: PropTypes.func.isRequired,
  onUnmount: PropTypes.func.isRequired,
};

function mapStateToProps(state) {
  return {
    expires: state.counter.expires,
  };
}

function mapDispatchToProps(dispatch) {
  return {
    onMount: (self) => {
      const timerFunc = self.props.onTickTimer.bind(self);
      self.setState({
        timer: window.setInterval(
          () => {
            timerFunc(self);
          },
          500
        ),
      });
      dispatch(fetchCounter());
    },
    onUnmount: (self) => {
      if (self.state.timer) {
        window.clearInterval(self.state.timer);
      }
      self.setState({
        timer: null,
      });
    },
    onTickTimer: (self) => {
      const { expires } = self.props;

      if (expires > (new Date()).getTime()) {
        return;
      }
      
      dispatch(fetchCounter());
    },
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(Counter);

import Impl from './schedule/Schedule';
import PropTypes from 'prop-types';
import React, { Component } from 'react';
import { connect } from 'react-redux';
import { fetchSchedule, scheduleTickTime } from '../../actions/schedule';

class Schedule extends Component {
  constructor (...args) {
    super(...args);
    this.state = {
      timer: null
    };
  }

  componentDidMount () {
    this.props.onMount(this);
  }

  componentWillUnmount () {
    this.props.onUnmount(this);
  }

  render () {
    return <Impl />;
  }
}

Schedule.propTypes = {
  expires: PropTypes.number.isRequired,
  onMount: PropTypes.func.isRequired,
  onTickTimer: PropTypes.func.isRequired,
  onUnmount: PropTypes.func.isRequired
};

function mapStateToProps (state) {
  return {
    expires: state.schedule.expires
  };
}

function mapDispatchToProps (dispatch) {
  return {
    onMount: (self) => {
      const timerFunc = self.props.onTickTimer.bind(self);
      self.setState({
        timer: window.setInterval(
          () => {
            timerFunc(self);
          },
          1000 // every second
        )
      });
      dispatch(fetchSchedule());
    },
    onUnmount: (self) => {
      if (self.state.timer) {
        window.clearInterval(self.state.timer);
      }
      self.setState({
        timer: null
      });
    },
    onTickTimer: (self) => {
      const { expires } = self.props;

      dispatch(scheduleTickTime());

      if (expires > (new Date()).getTime()) {
        return;
      }

      dispatch(fetchSchedule());
    }
  };
}

export default connect(mapStateToProps, mapDispatchToProps)(Schedule);

import Impl from './myLatestBattles/MyLatestBattles';
import PropTypes from 'prop-types';
import React, { Component } from 'react';
import { connect } from 'react-redux';
import { fetchMyLatestBattles } from '../../actions/myLatestBattles';

class MyLatestBattles extends Component {
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
    if (!this.props.isAvail) {
      return null;
    }

    return <Impl />;
  }
}

MyLatestBattles.propTypes = {
  expires: PropTypes.number.isRequired,
  isAvail: PropTypes.bool.isRequired,
  onMount: PropTypes.func.isRequired,
  onTickTimer: PropTypes.func.isRequired,
  onUnmount: PropTypes.func.isRequired,
};

function mapStateToProps(state) {
  return {
    expires: state.myLatestBattles.expires,
    isAvail: Boolean(
      state.myLatestBattles.data &&
      state.myLatestBattles.data.user &&
      state.myLatestBattles.data.battles.length > 0
    ),
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
          60 * 1000 // every minute
        ),
      });
      dispatch(fetchMyLatestBattles());
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
      
      dispatch(fetchMyLatestBattles());
    },
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(MyLatestBattles);

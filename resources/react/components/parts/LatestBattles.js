import Impl from './latestBattles/LatestBattles';
import React, { Component } from 'react';
import { connect } from 'react-redux';
import { fetchLatestBattles } from '../../actions/latestBattles';

class LatestBattles extends Component {
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
    if (!this.props.isAvail) {
      return null;
    }

    return <Impl />;
  }
}

function mapStateToProps (state) {
  return {
    expires: state.latestBattles.expires,
    isAvail: Boolean(
      state.latestBattles.data &&
      state.latestBattles.data.battles &&
      state.latestBattles.data.battles.length > 0
    )
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
          60 * 1000 // every minute
        )
      });
      dispatch(fetchLatestBattles());
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

      if (expires > (new Date()).getTime()) {
        return;
      }

      dispatch(fetchLatestBattles());
    }
  };
}

export default connect(mapStateToProps, mapDispatchToProps)(LatestBattles);

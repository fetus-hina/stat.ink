import Impl from './blog/BlogEntries';
import React, { Component } from 'react';
import { connect } from 'react-redux';
import { fetchBlogEntry } from '../../actions/blog';

class Blog extends Component {
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

function mapStateToProps (state) {
  return {
    expires: state.blog.expires
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
          500
        )
      });
      dispatch(fetchBlogEntry());
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

      dispatch(fetchBlogEntry());
    }
  };
}

export default connect(mapStateToProps, mapDispatchToProps)(Blog);

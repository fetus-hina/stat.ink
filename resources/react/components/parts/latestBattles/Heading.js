import React from 'react';
import { connect } from 'react-redux';

function Heading (props) {
  const { str } = props;

  return (
    <h2>{str}</h2>
  );
}

function mapStateToProps (state) {
  const data = state.latestBattles.data;
  return {
    str: data && data.translations ? data.translations.heading : 'Recent Battles'
  };
}

function mapDispatchToProps (/* dispatch */) {
  return {};
}

export default connect(mapStateToProps, mapDispatchToProps)(Heading);

import BattleCardList from '../BattleCardList';
import Heading from './Heading';
import PropTypes from 'prop-types';
import React from 'react';
import { connect } from 'react-redux';

function LatestBattles(props) {
  const { battles, reltime } = props;

  return (
    <div className="mb-3">
      <Heading />
      <BattleCardList
        battles={battles}
        reltime={reltime}
      />
    </div>
  );
}

LatestBattles.propTypes = {
  battles: PropTypes.array.isRequired,
  reltime: PropTypes.object.isRequired,
};

function mapStateToProps(state) {
  const data = state.latestBattles.data;
  return {
    battles: data.battles,
    reltime: data.translations.reltime,
  };
}

function mapDispatchToProps(/* dispatch */) {
  return {};
}

export default connect(mapStateToProps, mapDispatchToProps)(LatestBattles);

import BattleCardList from '../BattleCardList';
import Heading from './Heading';
import PropTypes from 'prop-types';
import React from 'react';
import { connect } from 'react-redux';

function MyLatestBattles(props) {
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

MyLatestBattles.propTypes = {
  battles: PropTypes.array.isRequired,
  reltime: PropTypes.object.isRequired,
};

function mapStateToProps(state) {
  const data = state.myLatestBattles.data;
  return {
    battles: data.battles,
    reltime: data.translations.reltime,
  };
}

function mapDispatchToProps(/* dispatch */) {
  return {};
}

export default connect(mapStateToProps, mapDispatchToProps)(MyLatestBattles);

import BattleCardList from '../BattleCardList';
import Heading from './Heading';
import PropTypes from 'prop-types';
import React from 'react';
import { connect } from 'react-redux';

function LatestBattles (props) {
  const { battles, fallbackImage, reltime } = props;

  return (
    <div className='mb-3'>
      <Heading />
      <BattleCardList
        battles={battles}
        fallbackImage={fallbackImage}
        reltime={reltime}
      />
    </div>
  );
}

LatestBattles.propTypes = {
  battles: PropTypes.array.isRequired,
  fallbackImage: PropTypes.string,
  reltime: PropTypes.object.isRequired
};

function mapStateToProps (state) {
  const data = state.latestBattles.data;
  return {
    battles: data.battles,
    fallbackImage: data.images.noImage,
    reltime: data.translations.reltime
  };
}

function mapDispatchToProps (/* dispatch */) {
  return {};
}

export default connect(mapStateToProps, mapDispatchToProps)(LatestBattles);

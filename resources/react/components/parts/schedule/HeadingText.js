import PropTypes from 'prop-types';
import React from 'react';
import { connect } from 'react-redux';

function HeadingText(props) {
  const { translations } = props;
  return translations ? translations.heading : 'Schedule';
}

HeadingText.propTypes = {
  translations: PropTypes.object,
};

function mapStateToProps(state) {
  return {
    translations: state.schedule.data
      ? state.schedule.data.translations
      : null,
  };
}

function mapDispatchToProps(/* dispatch */) {
  return {};
}

export default connect(mapStateToProps, mapDispatchToProps)(HeadingText);

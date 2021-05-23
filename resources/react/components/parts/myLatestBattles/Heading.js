import PropTypes from 'prop-types';
import React from 'react';
import esc from 'escape-html';
import { connect } from 'react-redux';

function Heading (props) {
  const { template, user } = props;

  const linkHTML = `<a href="${esc(user.url)}">${esc(user.name)}</a>`;
  const html = esc(template).replace('{name}', linkHTML);

  return (
    <h2 dangerouslySetInnerHTML={{
      __html: html
    }}
    />
  );
}

Heading.propTypes = {
  template: PropTypes.string.isRequired,
  user: PropTypes.object.isRequired
};

function mapStateToProps (state) {
  const data = state.myLatestBattles.data;
  return {
    template: data && data.translations ? data.translations.heading : '{name}\'s Battles',
    user: data.user
  };
}

function mapDispatchToProps (/* dispatch */) {
  return {};
}

export default connect(mapStateToProps, mapDispatchToProps)(Heading);

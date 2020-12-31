import CurrentTime from './CurrentTime';
import HeadingText from './HeadingText';
import Loading from './Loading';
import React from 'react';

export default function Heading() {
  return (
    <h2>
      <HeadingText />
      <CurrentTime />
      <Loading />
    </h2>
  );
}

Heading.propTypes = {
};

import Blog from './parts/Blog';
import LatestBattles from './parts/LatestBattles';
import MyLatestBattles from './parts/MyLatestBattles';
import React from 'react';
// import Schedule from './parts/Schedule';

export default function App () {
  return (
    <>
      <Blog />
      {/* <Schedule /> */}
      <MyLatestBattles />
      <LatestBattles />
    </>
  );
}

App.propTypes = {};

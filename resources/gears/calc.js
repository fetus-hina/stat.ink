/*! Copyright (C) 2016 AIZAWA Hina | MIT License */
(function (window) {
  'use strict';
  // http://wikiwiki.jp/splatoon2ch/?%A5%AE%A5%A2%A5%D1%A5%EF%A1%BC%B8%A1%BE%DA
  // reason = "oob" | "drown" | "fall" | other
  window.getRespawnTime = function (reason, mainCount, subCount) {
    switch (reason) {
      case 'oob':
      case 'fall':
      case 'drawn':
        return (function () {
          const x = Math.max(mainCount * 10 + subCount * 3 - 12, 0);
          const y = ((0.99 * x) - Math.pow(0.09 * x, 2)) / 30;
          const f = (1 - y) * 180 + 120 + (reason === 'drawn' ? 120 : 30);
          return f / 60;
        })();
      default:
        return (function () {
          const x = mainCount * 10 + subCount * 3;
          const y = ((0.99 * x) - Math.pow(0.09 * x, 2)) / 45;
          const f = (1 - y) * 360 + 30 + 120;
          return f / 60;
        })();
    }
  };

  window.getInkRecoveryTime = function (mainCount, subCount) {
    const x = mainCount * 10 + subCount * 3;
    const y = ((0.99 * x) - Math.pow(0.09 * x, 2)) / 75;
    const z = 100 / (180 * (1 - y));
    const f = Math.ceil(100 / z);
    return f / 60;
  };

  // defaultTime
  //      5: bubbler
  //      6: kraken
  //      12: echolocator
  window.getSpecialDuration = function (defaultTime, mainCount, subCount) {
    const x = mainCount * 10 + subCount * 3;
    const y = ((0.99 * x) - Math.pow(0.09 * x, 2)) / 75;
    return (1 + y) * defaultTime;
  };

  // frame:
  //      22: burst bomb
  //      33: splat & suction bomb
  //      38: seeker
  //      64: inkzooka
  window.getSpecialCount = function (frame, mainCount, subCount) {
    const f = window.getSpecialDuration(6, mainCount, subCount) * 60;
    return Math.ceil(f / frame);
  };

  window.getJumpPrepareTime = function (mainCount, subCount) {
    const x = mainCount * 10 + subCount * 3;
    const y = ((0.99 * x) - Math.pow(0.09 * x, 2)) / 60;
    return 60 * (1 - y) / 60;
  };

  window.getJumpPullUpTime = function (mainCount, subCount) {
    const x = mainCount * 10 + subCount * 3;
    const y = ((0.99 * x) - Math.pow(0.09 * x, 2)) / 60;
    return 120 * (1 - y) / 60;
  };

  window.getJumpPullDownTime = function (/* mainCount, subCount */) {
    return 40 / 60;
  };

  window.getJumpRigidTime = function (/* mainCount, subCount */) {
    return 10 / 60;
  };

  window.getJumpTime = function (mainCount, subCount) {
    return window.getJumpPrepareTime(mainCount, subCount) +
            window.getJumpPullUpTime(mainCount, subCount) +
            window.getJumpPullDownTime(mainCount, subCount) +
            window.getJumpRigidTime(mainCount, subCount);
  };

  window.getRunSpeed = function (mainCount, subCount) {
    const x = mainCount * 10 + subCount * 3;
    const y = ((0.99 * x) - Math.pow(0.09 * x, 2)) / 60;
    return 0.96 * (1 + y);
  };

  window.getSwimSpeed = function (mainCount, subCount) {
    const x = mainCount * 10 + subCount * 3;
    const y = ((0.99 * x) - Math.pow(0.09 * x, 2)) / 120;
    return 1.92 * (1 + y);
  };

  window.getBombThrow = function (mainCount, subCount) {
    const x = mainCount * 10 + subCount * 3;
    const y = ((0.99 * x) - Math.pow(0.09 * x, 2)) / 60;
    const z = 5.6 * (1 + y);
    return 28 * z;
  };

  // defaultPoint:
  //      Inkzooka: 220
  //      Echolocator/Kraken: 200
  //      Killer Wail: 160
  //      Others: 180
  window.getSpecialPoint = function (defaultPoint, mainCount, subCount) {
    const x = mainCount * 10 + subCount * 3;
    const y = ((0.99 * x) - Math.pow(0.09 * x, 2)) / 100;
    return Math.round(defaultPoint / (1 + y));
  };

  window.getSpecialSave = function (mainCount, subCount) {
    const x = mainCount * 10 + subCount * 3;
    const y = 0.5 - ((0.99 * x) - Math.pow(0.09 * x, 2)) / 60;
    return Math.max(0, y);
  };

  window.getInkSaverMain = function (mainCount, subCount) {
    const x = mainCount * 10 + subCount * 3;
    return 1 - ((0.99 * x) - Math.pow(0.09 * x, 2)) / 75;
  };

  window.getInkSaverSub = function (mainCount, subCount) {
    const x = mainCount * 10 + subCount * 3;
    return 1 - ((0.99 * x) - Math.pow(0.09 * x, 2)) / 120;
  };

  window.getAttackRatio = function (mainCount, subCount) {
    const x = mainCount * 10 + subCount * 3;
    const y = ((0.99 * x) - Math.pow(0.09 * x, 2)) / 100;
    return 1 + y;
  };

  window.getDefenseRatio = window.getAttackRatio;
})(window);

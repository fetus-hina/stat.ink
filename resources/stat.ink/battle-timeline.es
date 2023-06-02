jQuery($ => {
  const $graphs = $('.graph');
  window.battleEvents.sort((a, b) => a.at - b.at);

  // スペシャルのデータがあることを確認する
  const isIdentifiedWhoseSpecial = ((window.mySpecial !== null) && (window.battleEvents.filter(v => (v.at && v.type === 'special_weapon' && v.me)).length > 0));

  // 抱え落ち判定データを追加 {{{
  (() => {
    if (!isIdentifiedWhoseSpecial) {
      return;
    }

    let ignoreUntil = null;
    let charged = false;
    window.battleEvents
      .filter(v => (
        v.type === 'special_charged' ||
        v.type === 'dead' ||
        (v.type === 'special_weapon' && v.special_weapon === window.mySpecial && v.me)
      ))
      .forEach(v => {
        if (v.type === 'special_charged') {
          if (ignoreUntil === null || ignoreUntil < v.at) {
            charged = true;
            ignoreUntil = null;
          }
        } else if (v.type === 'special_weapon') {
          if (ignoreUntil === null || ignoreUntil < v.at) {
            charged = false;
            ignoreUntil = null;
          }
        } else if (v.type === 'dead') {
          v.__kakae = false;
          // 死んだあと1秒以内に貯まったらたぶん検出遅延
          const chargedAfterDeath = (window.battleEvents.filter(tmp => (tmp.type === 'special_charged' && tmp.at >= v.at && tmp.at <= v.at + 1)).length > 0);
          if (charged || chargedAfterDeath) {
            // 死んだあと1秒以内に使用していたらたぶん検出遅延
            const usedAfterDeath = (window.battleEvents.filter(tmp => (tmp.type === 'special_weapon' && v.special_weapon === window.mySpecial && v.me && tmp.at >= v.at && tmp.at <= v.at + 1)).length > 0);
            if (!usedAfterDeath) {
              v.__kakae = true;
            }
          }
          charged = false;
          ignoreUntil = v.at + 1;
        }
      });
  })(); // }}}

  function drawTimelineGraph () {
    const $graph_ = $graphs.filter('#timeline');

    // 塗った面積
    const inkedData = window.isNawabari
      ? window.battleEvents
        .filter(v => ((v.type === 'score' && v.score) || (v.type === 'point' && v.point)))
        .map(v => [v.at, v.type === 'score' ? v.score : v.point])
      : [];

    // ガチエリアのカウント
    // ペナルティは未実装
    // {{{
    let myAreaData = [];
    let hisAreaData = [];
    if (window.isGachi && window.ruleKey === 'area') {
      (() => {
        let lastTime = 0;
        let lastMy = 100;
        let lastHis = 100;
        window.battleEvents
          .filter(v => v.type === 'splatzone')
          .forEach(cur => {
            // 2.5 秒以上データが途切れていたら不自然なグラフを避けるために適当なデータをねつ造する
            if (cur.at - lastTime >= 2.5) {
              myAreaData.push([cur.at - 0.5, 100 - lastMy]);
              hisAreaData.push([cur.at - 0.5, 100 - lastHis]);
            }

            myAreaData.push([cur.at, 100 - cur.my_team_count]);
            hisAreaData.push([cur.at, 100 - cur.his_team_count]);

            lastTime = cur.at;
            lastMy = cur.my_team_count;
            lastHis = cur.his_team_count;
          });
      })();
      if (myAreaData.length > 0 || hisAreaData.length > 0) {
        myAreaData.unshift([0, 0]);
        hisAreaData.unshift([0, 0]);

        // エリア用ノイズリダイレクションの処理
        if ($graph_.attr('data-object-smoothing') === 'enable') {
          (() => {
            const processor = oldData => {
              let lastTime = 0;
              let lastCount = 0;
              const ret = [];
              oldData.forEach(cur => {
                let deltaT = Math.abs(cur[0] - lastTime);
                const deltaC = Math.abs(cur[1] - lastCount);
                if (deltaC > 5) {
                  deltaT = Math.max(deltaT, 1); // 計算の都合上最小の時間差を 1 秒とする（除算は数字が爆発して勾配がひどいことになるので）
                  const slope = deltaC / deltaT;
                  if (slope >= 5) {
                    // 勾配大きすぎるのでたぶん嘘
                    console && console.log('Noise reduction for SZ working: ignored, time=' + cur[0] + ' slope=' + slope);
                    return;
                  }
                }
                lastTime = cur[0];
                lastCount = cur[1];
                ret.push(cur);
              });
              return ret;
            };
            myAreaData = processor([].concat(myAreaData));
            hisAreaData = processor([].concat(hisAreaData));
          })();
        }
      }
    }
    // }}}

    // ヤグラ・ホコのカウント
    // {{{
    let objectiveData = (window.isGachi && window.ruleKey !== 'area')
      ? window.battleEvents
        .filter(v => v.type === 'objective')
        .map(v => [v.at, v.position])
      : [];

    // スムージングが有効なら objectiveData を差し替える
    if (objectiveData.length && $graph_.attr('data-object-smoothing') === 'enable') {
      if (!window.smoothedObjectiveData) {
        window.smoothedObjectiveData = (() => {
          console && console.log('Creating smoothed data');
          const rawData = [].concat(objectiveData);
          return objectiveData.map(data => [
            data[0],
            rawData // 前後 0.6 秒の平均値を真の値と見なす
              .filter(near => Math.abs(data[0] - near[0]) <= 0.6)
              .map(near => near[1])
              .avg()
          ]);
        })();
      }
      objectiveData = window.smoothedObjectiveData;
    }
    // }}}

    // window.battleEvents からヤグラ・ホコ時の対象位置→ポイント変換
    // isPositive: 自分のチームを対象にするとき true, 相手チームの時 false
    const createObjectPositionPoint = (isPositive) => { // {{{
      const coeffient = isPositive ? 1 : -1;
      // const list = objectiveData.map(v => {at: v[0], position: v[1] * coeffient});

      // ポイント更新したタイミングのリストを作成
      let max = 0;
      let lastEventAt = null; // ret に積まれている最後の時間と等しい場合は null
      const ret = [[0, 0]];
      objectiveData
        .map(v => ({
          at: v[0],
          position: v[1] * coeffient
        }))
        .forEach(v => {
          if (v.position > max) {
            // 勾配を正しく描画するために直前のイベントの時間とスコアを与える
            if (lastEventAt !== null) {
              ret.push([v.at, coeffient * max]);
            }

            // スコア更新おめ
            max = v.position;
            ret.push([v.at, coeffient * v.position]);
            lastEventAt = null;
          } else {
            lastEventAt = v.at;
          }
        });

      // 最後まで描画するために最後のイベントの時間のデータを作る
      lastEventAt = Math.max.apply(null, window.battleEvents.map(v => v.at));
      ret.push([lastEventAt, coeffient * max]);

      return ret;
    }; // }}}

    const pointColorFromHue = h => {
      const rgb = window.hsv2rgb(h, 0.48, 0.97);
      return `rgba(${rgb[0]},${rgb[1]},${rgb[2]},0.7)`;
    };

    const controlColorFromHue = h => {
      const rgb = window.hsv2rgb(h, 0.95, 0.50);
      return `rgba(${rgb[0]},${rgb[1]},${rgb[2]},0.7)`;
    };

    const inklingColorFromHue = h => {
      const rgb = window.hsv2rgb(h, 0.95, 0.50);
      return `rgba(${rgb[0]},${rgb[1]},${rgb[2]},0.5)`;
    };

    const objectPositionColorFromHues = (team1, team2) => {
      let hue = Math.round((team1 + team2) / 2);
      while (hue < 0) {
        hue += 360;
      }
      hue = hue % 360;
      const rgb = window.hsv2rgb(hue, 0.8, 0.7);
      return `rgb(${rgb[0]},${rgb[1]},${rgb[2]})`;
    };

    let streak = 0;
    let ignoreStreakUntil = 0;
    let combo = 0;
    let comboUntil = -10;
    const iconData = window.battleEvents
      .filter(v => {
        if (!v.at) {
          return false;
        }

        if (v.type === 'killed' || v.type === 'dead' || v.type === 'special_charged' || v.type === 'low_ink') {
          return true;
        }

        if (v.type === 'special_weapon') {
          return (
            v.special_weapon === 'barrier' ||
            v.special_weapon === 'bombrush' ||
            v.special_weapon === 'daioika' ||
            v.special_weapon === 'megaphone' ||
            v.special_weapon === 'supersensor' ||
            v.special_weapon === 'supershot' ||
            v.special_weapon === 'tornado'
          );
        }

        return false;
      })
      .map(v => {
        const size = Math.max(18, Math.ceil($graph_.height() * 0.075));
        if (v.type === 'dead') {
          return (() => {
            const reason = (v.reason && window.deathReasons[v.reason])
              ? window.deathReasons[v.reason]
              : null;
            combo = 0;
            comboUntil = -10;
            streak = 0;
            // 死んでいる間にkillが出た場合は無視する（同時killとかで起き得るはず）
            ignoreStreakUntil = v.at + (() => {
              const mainQR = window.gearAbilities.quick_respawn ? window.gearAbilities.quick_respawn.count.main : 0;
              const subQR = window.gearAbilities.quick_respawn ? window.gearAbilities.quick_respawn.count.sub : 0;
              return window.getRespawnTime(v.reason ? v.reason : 'unknown', mainQR, subQR);
            })();
            return [
              window.graphIcon[v.__kakae ? 'deadSp' : 'dead'].src,
              v.at,
              size,
              size,
              reason
            ];
          })();
        } else if (v.type === 'killed') {
          return (() => {
            const messages = [];
            if (v.at <= comboUntil) {
              ++combo;
            } else {
              combo = 1;
            }
            if (combo > 1) {
              messages.push(combo + ' ' + window.timelineTranslates.combos);
            }
            comboUntil = Math.max(comboUntil, v.at + 5.00);
            if (ignoreStreakUntil < v.at) {
              ++streak;
              if (streak > 1) {
                messages.push(streak + ' ' + window.timelineTranslates.streak);
              }
            }
            return [
              window.graphIcon[v.type].src,
              v.at,
              size,
              size,
              messages.length ? messages.join(' / ') : undefined
            ];
          })();
        } else if (v.type === 'special_weapon') {
          return [
            window.graphIcon.specials[v.special_weapon].src,
            v.at,
            size,
            size,
            window.specialNames[v.special_weapon],
            ($img) => {
              if (isIdentifiedWhoseSpecial) {
                $img.css({
                  opacity: (v.me && v.special_weapon === window.mySpecial) ? 1.0 : 0.4
                });
              }
            }
          ];
        } else if (v.type === 'special_charged') {
          return [
            window.graphIcon.specialCharged.src,
            v.at,
            size,
            size,
            window.timelineTranslates.spCharged
          ];
        } else if (v.type === 'low_ink') {
          return [
            window.graphIcon.lowInk.src,
            v.at,
            size,
            size,
            window.timelineTranslates.lowInk
          ];
        } else {
          return [
            window.graphIcon[v.type].src,
            v.at,
            size,
            size
          ];
        }
      });

    // デスした時の赤背景
    let markings = window.battleEvents
      .filter(v => (v.type === 'dead'))
      .map(v => {
        const mainQR = window.gearAbilities.quick_respawn ? window.gearAbilities.quick_respawn.count.main : 0;
        const subQR = window.gearAbilities.quick_respawn ? window.gearAbilities.quick_respawn.count.sub : 0;
        const respawnTime = window.getRespawnTime(v.reason ? v.reason : 'unknown', mainQR, subQR);
        return {
          xaxis: {
            from: v.at,
            to: v.at + respawnTime
          },
          color: 'rgba(255,200,200,0.6)'
        };
      });

    // キル等した時の縦線
    markings = markings.concat((() => {
      const colors = {
        killed: 'rgb(191,191,255)',
        dead: 'rgb(255,191,191)',
        special_charged: 'rgb(191,255,191)',
        low_ink: 'rgb(191,191,191)',
        special_weapon: 'rgb(255,191,255)'
      };
      const keys = Object.keys(colors);
      return window.battleEvents
        .filter(v => keys.indexOf(v.type) >= 0)
        .map(v => ({
          xaxis: {
            from: v.at,
            to: v.at
          },
          color: colors[v.type]
        }));
    })());

    if (inkedData.length > 0) {
      inkedData.unshift([0, null]);
      (() => {
        const lastEventAt = Math.max.apply(null, window.battleEvents.map(v => v.at));
        const lastScore = inkedData.slice(-1)[0][1];
        inkedData.push([lastEventAt, lastScore]);
      })();
    }
    if (objectiveData.length > 0) {
      objectiveData.unshift([0, 0]);
    }

    $graph_.each(function () {
      const $graph = $(this);
      if (inkedData.length < 1 && iconData.length < 1) {
        $graph.hide();
      }
      const data = [];
      if (inkedData.length > 0) {
        data.push({
          label: window.timelineTranslates.turfInked,
          data: inkedData,
          color: window.colorScheme.graph1
        });
      }
      if (myAreaData.length > 0 || hisAreaData.length > 0) {
        data.push({
          label: window.timelineTranslates.countGood,
          data: myAreaData,
          color: window.myTeamColorHue === null ? null : pointColorFromHue(window.myTeamColorHue),
          lines: {
            show: true,
            fill: true
          },
          shadowSize: 0
        });
        data.push({
          label: window.timelineTranslates.countBad,
          data: hisAreaData,
          color: window.hisTeamColorHue === null ? null : pointColorFromHue(window.hisTeamColorHue),
          lines: {
            show: true,
            fill: true
          },
          shadowSize: 0
        });
      }
      if (objectiveData.length > 0) {
        data.push({
          label: window.timelineTranslates.countGood,
          data: createObjectPositionPoint(true),
          color: window.myTeamColorHue === null ? null : pointColorFromHue(window.myTeamColorHue),
          lines: {
            show: true,
            fill: true,
            lineWidth: 1
          },
          shadowSize: 0
        });
        data.push({
          label: window.timelineTranslates.countBad,
          data: createObjectPositionPoint(false),
          color: window.hisTeamColorHue === null ? null : pointColorFromHue(window.hisTeamColorHue),
          lines: {
            show: true,
            fill: true,
            lineWidth: 1
          },
          shadowSize: 0
        });
        data.push({
          label: window.timelineTranslates.position,
          data: objectiveData,
          color: (window.myTeamColorHue === null || window.hisTeamColorHue === null)
            ? '#edc240'
            : objectPositionColorFromHues(window.myTeamColorHue, window.hisTeamColorHue),
          lines: {
            show: true,
            fill: false
          },
          shadowSize: 0
        });
      }

      if (window.myTeamColorHue !== null && window.myTeamColorHue !== null) {
        if (window.isGachi) {
          // Ranked Battle Events
          (() => {
            const rankedEvents = window.battleEvents
              .filter(v => {
                if (v.type !== 'ranked_battle_event') {
                  return false;
                }
                return (
                  (v.value === 'we_got') ||
                  (v.value === 'we_lost') ||
                  (v.value === 'they_got') ||
                  (v.value === 'they_lost')
                );
              });
            if (rankedEvents.length < 1) {
              return;
            }
            const dt = {
              neutral: [[0, 380]],
              we: [],
              they: []
            };
            let prevState = 'neutral';
            rankedEvents.forEach(cur => {
              const curState = (v => {
                switch (v) {
                  case 'we_got':
                    return 'we';
                  case 'they_got':
                    return 'they';
                  case 'we_lost':
                  case 'they_lost':
                    return 'neutral';
                }
              })(cur.value);
              if (prevState !== curState) {
                dt[prevState].push([cur.at, 380]);
                dt[prevState].push([cur.at + 0.0001, null]);
              }
              dt[curState].push([cur.at, 380]);
              prevState = curState;
            });
            // 最後まで描くために最後のイベントの時間までのデータを作る
            dt[prevState].push([
              window.battleEvents[window.battleEvents.length - 1].at,
              380
            ]);
            data.push({
              label: window.timelineTranslates.controlNoOne,
              data: dt.neutral,
              color: 'rgba(192,192,192,0.85)',
              yaxis: 2,
              lines: {
                fill: false,
                lineWidth: 7
              },
              shadowSize: 0
            });
            data.push({
              label: window.timelineTranslates.controlGood,
              data: dt.we,
              color: controlColorFromHue(window.myTeamColorHue),
              yaxis: 2,
              lines: {
                fill: false,
                lineWidth: 7
              },
              shadowSize: 0
            });
            data.push({
              label: window.timelineTranslates.controlBad,
              data: dt.they,
              color: controlColorFromHue(window.hisTeamColorHue),
              yaxis: 2,
              lines: {
                fill: false,
                lineWidth: 7
              },
              shadowSize: 0
            });
          })();
        }
        if (window.isNawabari) {
          (() => {
            const statusEvents = window.battleEvents
              .filter(v => {
                if (v.type !== 'game_status') {
                  return false;
                }
                switch (v.game_status) {
                  case 'neutral':
                  case 'advantage':
                  case 'disadvantage':
                  case 'winning':
                  case 'losing':
                    return true;
                  default:
                    return false;
                }
              });
            if (statusEvents.length < 1) {
              return;
            }
            const dt = {
              neutral: [[0, 380]],
              we: [],
              they: []
            };
            let prevState = 'neutral';
            statusEvents.forEach(cur => {
              const curState = (v => {
                switch (v) {
                  case 'advantage':
                  case 'winning':
                    return 'we';
                  case 'disadvantage':
                  case 'losing':
                    return 'they';
                  default:
                    return 'neutral';
                }
              })(cur.game_status);
              if (prevState !== curState) {
                dt[prevState].push([cur.at, 380]);
                dt[prevState].push([cur.at + 0.0001, null]);
              }
              dt[curState].push([cur.at, 380]);
              prevState = curState;
            });
            // 最後まで描くために最後のイベントの時間までのデータを作る
            dt[prevState].push([
              window.battleEvents[window.battleEvents.length - 1].at,
              380
            ]);
            data.push({
              label: window.timelineTranslates.neutral,
              data: dt.neutral,
              color: 'rgba(192,192,192,0.85)',
              yaxis: 2,
              lines: {
                fill: false,
                lineWidth: 7
              },
              shadowSize: 0
            });
            data.push({
              label: window.timelineTranslates.winningGood,
              data: dt.we,
              color: controlColorFromHue(window.myTeamColorHue),
              yaxis: 2,
              lines: {
                fill: false,
                lineWidth: 7
              },
              shadowSize: 0
            });
            data.push({
              label: window.timelineTranslates.winningBad,
              data: dt.they,
              color: controlColorFromHue(window.hisTeamColorHue),
              yaxis: 2,
              lines: {
                fill: false,
                lineWidth: 7
              },
              shadowSize: 0
            });
          })();
        }

        // Inklings Track Events
        (() => {
          const events = window.battleEvents.filter(v => (v.type === 'alive_inklings'));
          if (events.length > 0) {
            const members = [[], [], [], [], [], [], [], []];
            const alives = [false, false, false, false, false, false, false, false];
            events.forEach(d => {
              for (let i = 0; i < 4; ++i) {
                if (!d.my_team[i] && alives[i]) {
                  members[i].push([d.at - 0.001, 363 - i * 17]);
                  members[i].push([d.at, null]);
                }
                if (d.my_team[i]) {
                  members[i].push([d.at, 363 - i * 17]);
                }
                alives[i] = d.my_team[i];

                if (!d.his_team[i] && alives[i + 4]) {
                  members[i + 4].push([d.at - 0.001, 295 - i * 17]);
                  members[i + 4].push([d.at, null]);
                }
                if (d.his_team[i]) {
                  members[i + 4].push([d.at, 295 - i * 17]);
                }
                alives[i + 4] = d.his_team[i];
              }
            });

            // extend lines to end of battle (Event timing issue)
            (function () {
              const lastEventAt = Math.max.apply(null, window.battleEvents.map(v => v.at));
              for (let i = 0; i < 4; ++i) {
                if (alives[i]) {
                  members[i].push([lastEventAt, 363 - i * 17]);
                }

                if (alives[i + 4]) {
                  members[i + 4].push([lastEventAt, 295 - i * 17]);
                }
              }
            })();

            for (let i = 0; i < 8; ++i) {
              data.push({
                label: (i % 4 === 0)
                  ? ((i === 0)
                      ? window.timelineTranslates.goodGuys
                      : window.timelineTranslates.badGuys
                    )
                  : null,
                data: members[i],
                color: inklingColorFromHue(
                  (i < 4) ? window.myTeamColorHue : window.hisTeamColorHue
                ),
                yaxis: 2,
                lines: {
                  fill: false,
                  lineWidth: 3
                },
                shadowSize: 0
              });
            }
          }
        })();
      }

      // Special Charged
      (() => {
        if (!isIdentifiedWhoseSpecial) {
          return;
        }
        const events = window.battleEvents.filter(v => {
          if (v.type === 'special_charged' || v.type === 'dead') {
            return true;
          }
          if (v.type === 'special_weapon' && v.special_weapon === window.mySpecial && v.me) {
            return true;
          }
          return false;
        });

        let charged = false;
        const ret = [];
        const yPos = 2;
        events.forEach(ev => {
          if (ev.type === 'special_charged') {
            charged = true;
            ret.push([ev.at, yPos]);
          } else if (charged) { // type is 'special_weapon' or 'dead'
            charged = false;
            ret.push([ev.at - 0.001, yPos]);
            ret.push([ev.at, null]);
          }
        });

        // チャージしたまま終わったらグラフの最後まで伸ばす
        if (charged) {
          const lastEventAt = Math.max.apply(null, window.battleEvents.map(v => v.at));
          ret.push([lastEventAt, yPos]);
        }

        if (ret.length) {
          data.push({
            label: null,
            data: ret,
            color: '#1c1',
            yaxis: 2,
            lines: {
              fill: false,
              lineWidth: 5
            },
            shadowSize: 0
          });
        }
      })();

      // Special %
      (() => {
        const events = window.battleEvents.filter(v => {
          return (
            v.at !== undefined &&
            v.type === 'special%' &&
            v.point !== undefined
          );
        });
        if (events.length > 0) {
          data.push({
            label: window.timelineTranslates.specialPct,
            data: (() => {
              const tmp = events.map(a => [a.at, a.point]);
              tmp.push([0, 0]);
              tmp.sort((a, b) => a[0] - b[0]);
              return tmp;
            })(),
            color: '#888',
            yaxis: 2,
            lines: {
              fill: false,
              lineWidth: 1
            },
            shadowSize: 1
          });
        }
      })();

      data.push({
        data: iconData,
        icons: {
          show: true,
          tooltip: (x, $this, userData) => {
            const t = Math.floor(x);
            const m = Math.floor(t / 60);
            const s = t % 60;
            let value = m + ':' + (s < 10 ? '0' + s : s);
            if (typeof userData === 'string') {
              value += ' / ' + userData;
            }
            $this.attr('title', value).tooltip({ container: 'body' });
          }
        }
      });

      $.plot($graph, data, {
        xaxis: {
          min: 0,
          minTickSize: 30,
          tickFormatter: v => {
            v = Math.floor(v);
            const m = Math.floor(v / 60);
            const s = Math.floor(v % 60);
            return m + ':' + (s < 10 ? '0' + s : s);
          }
        },
        yaxis: {
          minTickSize: window.isNawabari ? 100 : 10,
          min: (window.isNawabari || window.ruleKey === 'area') ? 0 : -100,
          max: window.isNawabari ? null : 100,
          show: true
        },
        y2axis: {
          min: 0,
          max: 400,
          show: false
        },
        legend: {
          container: $('#timeline-legend')
        },
        series: {
          lines: {
            show: true,
            fill: true
          }
        },
        grid: {
          markings
        }
      });
    });
  }

  let timerId = null;
  $(window)
    .resize(() => {
      if (timerId !== null) {
        clearTimeout(timerId);
        timerId = null;
      }
      timerId = setTimeout(
        () => {
          timerId = null;
          $graphs.height($graphs.width() * 9 / 16);
          drawTimelineGraph();
        },
        33
      );
    })
    .resize();
});

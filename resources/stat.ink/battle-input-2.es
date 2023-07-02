/*! Copyright (C) 2015-2019 AIZAWA Hina | MIT License */
($ => {
  $(() => {
    let initialized = false;
    let nextStageArrives;
    let stageTimerId;
    const $modal = $('#inputModal2');
    const translateTable = $modal.data('translate');
    const translate = text => (translateTable[text] || text);
    const $selectWeapons = $('.battle-input2-form--weapons', $modal);
    const $buttonStages = $('.battle-input2-form--stages', $modal);
    const $buttonResults = $('.battle-input2-form--result', $modal);
    const $buttonKOs = $('.battle-input2-form--knock_out', $modal);
    const $regularSubmit = $('#battle-input2-form--regular--submit', $modal);
    const $rankedSubmit = $('#battle-input2-form--ranked--submit', $modal);
    const $festSubmit = $('#battle-input2-form--fest--submit', $modal);
    const updateStageTimer = () => {
      $('.next-stages-will-arrive-in-2--value').text(
        (() => {
          if (!nextStageArrives) {
            return '-:--:--';
          }
          const now = Date.now() / 1000;
          const inSec_ = Math.max(nextStageArrives - now, 0);
          const inSec = Math.floor(inSec_);
          const colon = inSec_ - inSec >= 0.5 ? ':' : ' ';
          const hours = Math.floor(inSec / 3600);
          const minutes = Math.floor(inSec / 60) % 60;
          const seconds = inSec % 60;
          const zero = v => (v < 10 ? '0' + v : v);
          return hours + colon + zero(minutes) + colon + zero(seconds);
        })()
      );
    };
    const stopStageTimer = () => {
      if (stageTimerId) {
        window.clearInterval(stageTimerId);
      }
      stageTimerId = undefined;
      window.setTimeout(updateStageTimer, 1);
    };
    const runStageTimer = () => {
      stopStageTimer();
      window.setTimeout(updateStageTimer, 1);
      stageTimerId = window.setInterval(updateStageTimer, 500);
    };
    const generateUuid = () => window.crypto.randomUUID();
    const updateUuidRegular = () => {
      $('#battle-input2-form--regular--uuid').val(generateUuid());
    };
    const updateUuidRanked = () => {
      $('#battle-input2-form--ranked--uuid').val(generateUuid());
    };
    const updateUuidFest = () => {
      $('#battle-input2-form--fest--uuid').val(generateUuid());
    };
    const serializeForm = $form => {
      const ret = {};
      $.each($form.serializeArray(), (i, obj) => {
        const name = obj.name;
        let value = obj.value;
        if (name && value !== null && value !== undefined) {
          value = String(value).trim();
          if (value !== '') {
            ret[name] = value;
          }
        }
      });
      return ret;
    };
    const refresh = () => {
      $.ajax('/api/internal/current-data2', {
        cache: false,
        method: 'GET',
        dataType: 'json',
        success: json => {
          // ステージタイマー用データ
          nextStageArrives = Math.floor(Date.now() / 1000 + json.current.period.next);
          runStageTimer();

          // ステージ変更時にinitializedフラグを落とす仕込み
          let timerId;
          (() => {
            if (timerId) {
              clearTimeout(timerId);
              timerId = null;
            }
            timerId = setTimeout(() => {
              timerId = null;
              initialized = false;
            }, json.current.period.next * 1000);
          })();

          $.each(['regular', 'ranked', 'fest'], (i, modeKey) => {
            if (json.current[modeKey] && json.current[modeKey].rule) {
              const rule = json.current[modeKey].rule;
              const $inputs = $('input', $modal);
              $inputs
                .filter(function () { return $(this).attr('id') === 'battle-input2-form--' + modeKey + '--rule'; })
                .val(rule.key);
              $inputs
                .filter(function () { return $(this).attr('id') === 'battle-input2-form--' + modeKey + '--rule--label'; })
                .val(rule.name);
            }

            // ステージ用の <button> のラベルを正しく設定する
            // 広い画面ではフルのステージ名を、狭い画面では短縮のステージ名を表示する
            if (json.current[modeKey] && json.current[modeKey].maps.length) {
              const $buttons = $buttonStages.filter(function () {
                return $(this).attr('data-game-mode') === modeKey;
              });
              $buttons.each(function (index) {
                const $this = $(this);
                const key = json.current[modeKey].maps[index];
                if (key) {
                  $this
                    .attr('data-value', key)
                    .attr('data-image', json.maps[key].image) // 今のところ使う予定なし
                    .empty()
                    .append($('<span>', { class: 'hidden-xs' }).text(json.maps[key].name))
                    .append($('<span>', { class: 'visible-xs-inline' }).text(json.maps[key].shortName));
                }
              });
            }
          });

          // ブキ一覧の <select> の <option> を作成する
          $selectWeapons.each(function () {
            const $this = $(this);
            $this.empty();

            // お気に入りのブキ
            if (json.favWeapons) {
              $this.append((function () {
                const $group = $('<optgroup>', { label: translate('Favorite Weapons') });
                $.each(json.favWeapons, function (i, weapon) {
                  $group.append(
                    $('<option>', { label: weapon.name, value: weapon.key }).text(weapon.name)
                  );
                });
                return $group;
              })());
            }

            // 種類別
            $.each(json.weapons, function (key, type) {
              $this.append((function () {
                const $group = $('<optgroup>', { label: type.name });
                $.each(type.list, function (key, weapon) {
                  $group.append(
                    $('<option>', { label: weapon.name, value: key }).text(weapon.name)
                  );
                });
                return $group;
              })());
            });
          });

          initialized = true;
        }
      });
    };

    const validateRegular = function () {
      const $form = $('form#battle-input2-form--regular');
      const $requires = $([
        '#battle-input2-form--regular--rule',
        '#battle-input2-form--regular--lobby',
        '#battle-input2-form--regular--weapon',
        '#battle-input2-form--regular--stage',
        '#battle-input2-form--regular--result'
      ].join(','), $form);
      const $empty = $requires.filter(function () {
        return $(this).val() === '';
      });
      if ($empty.length) {
        return false;
      }

      const $elems = $([
        '#battle-input2-form--regular--kill-or-assist',
        '#battle-input2-form--regular--special'
      ].join(','), $form);
      let valid = true;
      $elems.each((i, el) => {
        const $elem = $(el);
        const value = String($elem.val()).trim();
        if (value !== '') {
          if (!value.match(/^\d+$/)) {
            valid = false;
            return;
          }
          const intValue = parseInt(value, 10);
          if (intValue < 0 || intValue > 99) {
            valid = false;
          }
        }
      });
      if (!valid) {
        return false;
      }
      return true;
    };
    const validateRanked = function () {
      const $form = $('form#battle-input2-form--ranked');
      const $requires = $([
        '#battle-input2-form--ranked--rule',
        '#battle-input2-form--ranked--lobby',
        '#battle-input2-form--ranked--weapon',
        '#battle-input2-form--ranked--stage',
        '#battle-input2-form--ranked--result',
        '#battle-input2-form--ranked--knock_out'
      ].join(','), $form);
      const $empty = $requires.filter(function () {
        return $(this).val() === '';
      });
      if ($empty.length) {
        return false;
      }

      const $elems = $([
        '#battle-input2-form--ranked--kill-or-assist',
        '#battle-input2-form--ranked--special'
      ].join(','), $form);
      let valid = true;
      $elems.each((i, el) => {
        const $elem = $(el);
        const value = String($elem.val()).trim();
        if (value !== '') {
          if (!value.match(/^\d+$/)) {
            valid = false;
            return;
          }
          const intValue = parseInt(value, 10);
          if (intValue < 0 || intValue > 99) {
            valid = false;
          }
        }
      });
      if (!valid) {
        return false;
      }
      return true;
    };
    const validateFest = function () {
      const $form = $('form#battle-input2-form--fest');
      const $requires = $([
        '#battle-input2-form--fest--rule',
        '#battle-input2-form--fest--lobby',
        '#battle-input2-form--fest--weapon',
        '#battle-input2-form--fest--stage',
        '#battle-input2-form--fest--result'
      ].join(','), $form);
      const $empty = $requires.filter(function () {
        return $(this).val() === '';
      });
      if ($empty.length) {
        return false;
      }

      let $elem;
      let value;

      $elem = $('#battle-input2-form--fest--kill-or-assist', $form);
      value = ($elem.val() + '').trim();
      if (value !== '') {
        if (!value.match(/^\d+$/)) {
          return false;
        }
        value = parseInt(value, 10);
        if (value < 0 || value > 99) {
          return false;
        }
      }

      $elem = $('#battle-input2-form--fest--special', $form);
      value = ($elem.val() + '').trim();
      if (value !== '') {
        if (!value.match(/^\d+$/)) {
          return false;
        }
        value = parseInt(value, 10);
        if (value < 0 || value > 99) {
          return false;
        }
      }
      return true;
    };
    const updateAgentVersion = () => {
      const $input = $('input[name="agent_version"]');
      $input.val((detect => {
        // {{{
        const comments = [
          $input.attr('data-revision')
        ];

        if (detect.os && detect.os.name) {
          comments.push(detect.os.name);
          if (detect.os.name === 'iOS' && detect.platform && detect.platform.model) {
            comments.push(detect.platform.model);
          }
        }

        if (detect.browser && detect.browser.name) {
          comments.push(detect.browser.name);
        }

        return $input.attr('data-version') + ' (' + comments.join(', ') + ')';
        // }}}
      })(window.bowser.parse(window.navigator.userAgent || '')));
    };

    // 表示時に（必要であれば）通信をして画面要素を更新する
    $modal.on('show.bs.modal', () => {
      if (!initialized) {
        refresh();
        updateAgentVersion();
      }
      updateUuidRegular();
      updateUuidRanked();
      updateUuidFest();
    });

    // ステージボタンがクリックされた時、電文用の <input type="hidden"> を更新する
    // また、class を変更して選択されているかのように見せる
    $buttonStages.click(function () {
      const $this = $(this);
      const $input = $('input', $modal).filter(function () { return $(this).attr('id') === $this.attr('data-target'); });
      $input.val($this.attr('data-value')).change();

      $buttonStages
        .filter(function () { return $this.attr('data-target') === $(this).attr('data-target'); })
        .removeClass('btn-success')
        .addClass('btn-default');
      $this
        .removeClass('btn-default')
        .addClass('btn-success');
    });

    // 勝ち/負けボタンがクリックされた時、電文用の <input type="hidden"> を更新する
    // また、class を変更して選択されているかのように見せる
    $buttonResults.click(function () {
      const $this = $(this);
      const $input = $('input', $modal).filter(function () { return $(this).attr('id') === $this.attr('data-target'); });
      $input.val($this.attr('data-value')).change();

      $buttonResults
        .filter(function () { return $this.attr('data-target') === $(this).attr('data-target'); })
        .removeClass('btn-info')
        .removeClass('btn-danger')
        .addClass('btn-default');
      $this
        .removeClass('btn-default')
        .addClass($this.attr('data-value') === 'win' ? 'btn-info' : 'btn-danger');
    });

    // KOボタンがクリックされた時、電文用の <input type="hidden"> を更新する
    // また、class を変更して選択されているかのように見せる
    $buttonKOs.click(function () {
      const $this = $(this);
      const $input = $('input', $modal).filter(function () { return $(this).attr('id') === $this.attr('data-target'); });
      $input.val($this.attr('data-value')).change();

      $buttonKOs
        .filter(function () { return $this.attr('data-target') === $(this).attr('data-target'); })
        .removeClass('btn-info')
        .removeClass('btn-danger')
        .addClass('btn-default');
      $this
        .removeClass('btn-default')
        .addClass($this.attr('data-value') === 'yes' ? 'btn-danger' : 'btn-info');
    });

    // レギュラーの送信ボタン押下処理
    $regularSubmit.click(function () {
      const $this = $(this);
      const $form = $('#' + $this.attr('data-form') + ' form');
      if (!$form.length) {
        return;
      }
      $('#battle-input2-form--regular--end_at').val(Math.floor(Date.now() / 1000));
      $this.prop('disabled', true);
      $.ajax('/api/v2/battle', {
        method: 'POST',
        headers: {
          Authorization: 'Bearer ' + $form.attr('data-apikey')
        },
        data: JSON.stringify(serializeForm($form)),
        contentType: 'application/json',
        processData: false,
        success: () => {
          const clear = [
            'battle-input2-form--regular--kill-or-assist',
            'battle-input2-form--regular--point',
            'battle-input2-form--regular--result',
            'battle-input2-form--regular--special'
          ];
          $.each(clear, (i, id) => {
            $('#' + id).val('');
          });
          $buttonStages
            .filter('[data-target="battle-input2-form--regular--stage"]')
            .removeClass('btn-success')
            .addClass('btn-default');
          $buttonResults
            .filter('[data-target="battle-input2-form--regular--result"]')
            .removeClass('btn-info')
            .removeClass('btn-danger')
            .addClass('btn-default');
          $this.prop('disabled', false);
        },
        error: () => {
          window.alert('Could not create a new battle record.');
          $this.prop('disabled', false);
        },
        complete: () => {
          updateUuidRegular();
        }
      });
    });
    // ガチマッチの送信ボタン押下処理
    $rankedSubmit.click(function () {
      const $this = $(this);
      const $form = $('#' + $this.attr('data-form') + ' form');
      if (!$form.length) {
        return;
      }
      $('#battle-input2-form--ranked--end_at').val(Math.floor(Date.now() / 1000));
      $this.prop('disabled', true);
      $.ajax('/api/v2/battle', {
        method: 'POST',
        headers: {
          Authorization: 'Bearer ' + $form.attr('data-apikey')
        },
        data: JSON.stringify(serializeForm($form)),
        contentType: 'application/json',
        processData: false,
        success: () => {
          const clear = [
            'battle-input2-form--ranked--kill-or-assist',
            'battle-input2-form--ranked--result',
            'battle-input2-form--ranked--knock_out',
            'battle-input2-form--ranked--special'
          ];
          $.each(clear, (i, id) => {
            $('#' + id).val('');
          });
          $buttonResults
            .filter('[data-target="battle-input2-form--ranked--result"]')
            .removeClass('btn-info')
            .removeClass('btn-danger')
            .addClass('btn-default');
          $buttonKOs
            .filter('[data-target="battle-input2-form--ranked--knock_out"]')
            .removeClass('btn-info')
            .removeClass('btn-danger')
            .addClass('btn-default');

          // ウデマエをずらす
          $('#battle-input2-form--ranked--rank').val($('#battle-input2-form--ranked--rank-after').val());

          $this.prop('disabled', false);
        },
        error: () => {
          window.alert('Could not create a new battle record.');
          $this.prop('disabled', false);
        },
        complete: () => {
          updateUuidRanked();
        }
      });
    });
    // フェスの送信ボタン押下処理
    $festSubmit.click(function () {
      const $this = $(this);
      const $form = $('#' + $this.attr('data-form') + ' form');
      if (!$form.length) {
        return;
      }
      $('#battle-input2-form--fest--end_at').val(Math.floor(Date.now() / 1000));
      $this.prop('disabled', true);
      $.ajax('/api/v2/battle', {
        method: 'POST',
        headers: {
          Authorization: 'Bearer ' + $form.attr('data-apikey')
        },
        data: JSON.stringify(serializeForm($form)),
        contentType: 'application/json',
        processData: false,
        success: () => {
          const clear = [
            'battle-input2-form--fest--kill-or-assist',
            'battle-input2-form--fest--point',
            'battle-input2-form--fest--result',
            'battle-input2-form--fest--special',
            'battle-input2-form--fest--stage'
          ];
          $.each(clear, (i, id) => {
            $('#' + id).val('');
          });
          $buttonStages
            .filter('[data-target="battle-input2-form--fest--stage"]')
            .removeClass('btn-success')
            .addClass('btn-default');
          $buttonResults
            .filter('[data-target="battle-input2-form--fest--result"]')
            .removeClass('btn-info')
            .removeClass('btn-danger')
            .addClass('btn-default');
          $this.prop('disabled', false);
        },
        error: () => {
          window.alert('Could not create a new battle record.');
          $this.prop('disabled', false);
        },
        complete: () => {
          updateUuidFest();
        }
      });
    });

    // 変更を検知して送信ボタンの状態を切り替える
    (() => {
      // 変更即反映できる方々
      const idList = [
        '#battle-input2-form--regular--rule',
        '#battle-input2-form--regular--lobby',
        '#battle-input2-form--regular--weapon',
        '#battle-input2-form--regular--stage',
        '#battle-input2-form--regular--result'
      ];
      $(idList.join(',')).change(() => {
        $regularSubmit.prop('disabled', !validateRegular());
      });

      // ユーザ入力のためにキー入力をベースにする方々
      let timerId;
      const idList2 = [
        '#battle-input2-form--regular--kill-or-assist',
        '#battle-input2-form--regular--special'
      ];
      $(idList2.join(',')).keydown(() => {
        if (timerId) {
          window.clearTimeout(timerId);
        }
        timerId = window.setTimeout(() => {
          $regularSubmit.prop('disabled', !validateRegular());
        }, 50);
      });
    })();
    (() => {
      // 変更即反映できる方々
      const idList = [
        '#battle-input2-form--fest--rule',
        '#battle-input2-form--fest--lobby',
        '#battle-input2-form--fest--weapon',
        '#battle-input2-form--fest--stage',
        '#battle-input2-form--fest--result'
      ];
      $(idList.join(',')).change(() => {
        $festSubmit.prop('disabled', !validateFest());
      });

      // ユーザ入力のためにキー入力をベースにする方々
      let timerId;
      const idList2 = [
        '#battle-input2-form--fest--kill-or-assist',
        '#battle-input2-form--fest--special'
      ];
      $(idList2.join(',')).keydown(() => {
        if (timerId) {
          window.clearTimeout(timerId);
        }
        timerId = window.setTimeout(() => {
          $festSubmit.prop('disabled', !validateFest());
        }, 50);
      });
    })();
    (() => {
      // 変更即反映できる方々
      const idList = [
        '#battle-input2-form--ranked--rule',
        '#battle-input2-form--ranked--lobby',
        '#battle-input2-form--ranked--weapon',
        '#battle-input2-form--ranked--stage',
        '#battle-input2-form--ranked--result',
        '#battle-input2-form--ranked--knock_out',
        '#battle-input2-form--ranked--rank',
        '#battle-input2-form--ranked--rank-after'
      ];
      $(idList.join(',')).change(() => {
        $rankedSubmit.prop('disabled', !validateRanked());
      });

      // ユーザ入力のためにキー入力をベースにする方々
      let timerId;
      const idList2 = [
        '#battle-input2-form--ranked--kill-or-assist',
        '#battle-input2-form--ranked--special'
      ];
      $(idList2.join(',')).keydown(() => {
        if (timerId) {
          window.clearTimeout(timerId);
        }
        timerId = window.setTimeout(() => {
          $rankedSubmit.prop('disabled', !validateRanked());
        }, 50);
      });
    })();

    // ナビゲーションバーの登録ボタン
    if ($modal.length) {
      $('#battle-input2-btn')
        .prop('disabled', false)
        .click(() => {
          $modal.modal({
            backdrop: 'static', // Do not close even clicked the background
            keyboard: false // Do not close even pressed Escape key
          });
        });
    }
  });
})(jQuery);

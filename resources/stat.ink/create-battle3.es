/*! Copyright (C) 2026 AIZAWA Hina | MIT License */

((window, $) => {
  $(() => {
    const config = window.__createBattle3Config || {};
    const i18n = config.i18n || {};
    const $form = $('#create-battle3-form');
    if (!$form.length) {
      return;
    }

    const $alert = $('#create-battle3-alert');
    const $submit = $('#create-battle3-submit');
    const $stageControl = $('#create-battle3-stage-control');
    const stageDropdownHtml = $stageControl.html();
    const $ruleSelect = $('#create-battle3-rule');
    const ruleOptions = $ruleSelect.find('option').map(function () {
      return { value: this.value, text: $(this).text() };
    }).get();
    const baseRulesByLobby = {
      regular: ['nawabari'],
      splatfest_challenge: ['nawabari'],
      splatfest_open: ['nawabari', 'tricolor'],
      bankara_challenge: ['area', 'yagura', 'hoko', 'asari'],
      bankara_open: ['area', 'yagura', 'hoko', 'asari'],
      xmatch: ['area', 'yagura', 'hoko', 'asari']
    };
    let scheduleData = null;
    let scheduleFetchPromise = null;

    const fieldsToClearOnSuccess = [
      'kill_or_assist',
      'assist',
      'death',
      'special',
      'inked'
    ];

    const generateUuid = () => {
      if (window.crypto && typeof window.crypto.randomUUID === 'function') {
        return window.crypto.randomUUID();
      }
      const bytes = new Uint8Array(16);
      window.crypto.getRandomValues(bytes);
      bytes[6] = (bytes[6] & 0x0f) | 0x40;
      bytes[8] = (bytes[8] & 0x3f) | 0x80;
      const hex = Array.from(bytes, b => b.toString(16).padStart(2, '0')).join('');
      return [
        hex.slice(0, 8),
        hex.slice(8, 12),
        hex.slice(12, 16),
        hex.slice(16, 20),
        hex.slice(20)
      ].join('-');
    };

    const showAlert = (klass, message) => {
      $alert
        .removeClass('alert-success alert-danger')
        .addClass(klass)
        .text(message)
        .show();
    };
    const hideAlert = () => {
      $alert.hide();
    };

    const fieldValueOf = name => {
      const $checked = $form.find('input[type="radio"][name="' + name + '"]:checked');
      if ($checked.length) {
        return ($checked.val() || '').toString().trim();
      }
      const $el = $form.find('[name="' + name + '"]:not([type="radio"])').first();
      return $el.length ? ($el.val() || '').toString().trim() : '';
    };
    const intValueOf = name => {
      const v = fieldValueOf(name);
      if (v === '') return null;
      const n = parseInt(v, 10);
      return Number.isFinite(n) ? n : null;
    };

    const buildPayload = () => {
      const data = {
        uuid: generateUuid(),
        agent: config.agent,
        agent_version: config.agentVersion,
        automated: 'no'
      };

      const stringFields = ['lobby', 'rule', 'stage', 'weapon', 'result'];
      stringFields.forEach(name => {
        const v = fieldValueOf(name);
        if (v !== '') data[name] = v;
      });

      const koa = intValueOf('kill_or_assist');
      const assist = intValueOf('assist');
      if (koa !== null) data.kill_or_assist = koa;
      if (assist !== null) data.assist = assist;
      if (koa !== null && assist !== null) {
        data.kill = Math.max(0, koa - assist);
      }

      const intFields = ['death', 'special', 'inked'];
      intFields.forEach(name => {
        const v = intValueOf(name);
        if (v !== null) data[name] = v;
      });

      return data;
    };

    const formatErrorDetail = error => {
      if (!error) return '';
      if (typeof error === 'string') return error;
      try {
        return Object.entries(error).map(([k, v]) =>
          k + ': ' + (Array.isArray(v) ? v.join(', ') : String(v))
        ).join(' / ');
      } catch (_e) {
        return '';
      }
    };

    const updateRadioButtonStyles = name => {
      const $radios = $form.find('input[type="radio"][name="' + name + '"]');
      $radios.each(function () {
        const $label = $(this).closest('label');
        if (this.checked) {
          $label.removeClass('btn-default').addClass('btn-primary');
        } else {
          $label.removeClass('btn-primary').addClass('btn-default');
        }
      });
    };

    const resetRadioButtonGroup = name => {
      const $radios = $form.find('input[type="radio"][name="' + name + '"]');
      if (!$radios.length) {
        return;
      }
      $radios.prop('checked', false);
      $radios.closest('label').removeClass('active');
      const $defaultRadio = $radios.filter('[value=""]');
      if ($defaultRadio.length) {
        $defaultRadio.prop('checked', true);
        $defaultRadio.closest('label').addClass('active');
      }
      updateRadioButtonStyles(name);
    };

    $form.on('change', 'input[type="radio"]', function () {
      updateRadioButtonStyles(this.name);
    });

    const buildRadioButton = (value, label, isDefault) => {
      const $input = $('<input>', {
        type: 'radio',
        name: 'stage',
        value,
        autocomplete: 'off'
      });
      if (isDefault) {
        $input.prop('checked', true);
      }
      const $label = $('<label>', {
        class: 'btn ' + (isDefault ? 'btn-primary active' : 'btn-default')
      });
      $label.append($input).append(document.createTextNode(label));
      return $label;
    };

    const renderStageRadios = stages => {
      const $group = $('<div>', {
        class: 'btn-group btn-group-justified',
        role: 'group'
      }).attr('data-toggle', 'buttons');
      stages.forEach(stage => {
        if (stage && stage.key) {
          $group.append(buildRadioButton(stage.key, stage.name || stage.key, false));
        }
      });
      $stageControl.empty().append($group);
    };

    const renderStageDropdown = () => {
      $stageControl.html(stageDropdownHtml);
    };

    const applyScheduleToStage = () => {
      const lobbyKey = fieldValueOf('lobby');
      const stages = scheduleData &&
        scheduleData.current &&
        scheduleData.current.lobbies &&
        scheduleData.current.lobbies[lobbyKey] &&
        scheduleData.current.lobbies[lobbyKey].stages;
      if (Array.isArray(stages) && stages.length > 0) {
        renderStageRadios(stages);
      } else {
        renderStageDropdown();
      }
    };

    const applyLobbyToRule = () => {
      const lobbyKey = fieldValueOf('lobby');
      const scheduleRuleKey = scheduleData &&
        scheduleData.current &&
        scheduleData.current.lobbies &&
        scheduleData.current.lobbies[lobbyKey] &&
        scheduleData.current.lobbies[lobbyKey].rule &&
        scheduleData.current.lobbies[lobbyKey].rule.key;
      const allowedKeys = scheduleRuleKey
        ? [scheduleRuleKey]
        : (Object.prototype.hasOwnProperty.call(baseRulesByLobby, lobbyKey)
            ? baseRulesByLobby[lobbyKey]
            : null);

      const previousValue = $ruleSelect.val();
      $ruleSelect.empty();
      ruleOptions.forEach(opt => {
        if (
          opt.value === '' ||
          allowedKeys === null ||
          allowedKeys.indexOf(opt.value) !== -1
        ) {
          $ruleSelect.append($('<option>', { value: opt.value, text: opt.text }));
        }
      });

      if (scheduleRuleKey) {
        $ruleSelect.val(scheduleRuleKey);
      } else if (
        previousValue !== '' &&
        allowedKeys !== null &&
        allowedKeys.indexOf(previousValue) === -1
      ) {
        $ruleSelect.val('');
      } else {
        $ruleSelect.val(previousValue);
      }
    };

    const fetchSchedule = () => {
      if (scheduleData !== null) {
        return Promise.resolve(scheduleData);
      }
      if (scheduleFetchPromise) {
        return scheduleFetchPromise;
      }
      if (!config.scheduleUrl) {
        return Promise.resolve(null);
      }
      scheduleFetchPromise = window.fetch(config.scheduleUrl, {
        credentials: 'same-origin',
        headers: { Accept: 'application/json' }
      })
        .then(response => response.ok ? response.json() : null)
        .catch(() => null)
        .then(data => {
          scheduleData = data || false;
          return scheduleData;
        });
      return scheduleFetchPromise;
    };

    const refetchSchedule = () => {
      scheduleData = null;
      scheduleFetchPromise = null;
      return fetchSchedule();
    };

    const applyLobbySpecificFields = () => {
      applyLobbyToRule();
      applyScheduleToStage();
    };

    $form.on('change', '[name="lobby"]', applyLobbySpecificFields);

    $('#create-battle3-modal').on('show.bs.modal', () => {
      fetchSchedule().then(applyLobbySpecificFields);
    });

    const $refreshButton = $('#create-battle3-refresh-schedule');
    $refreshButton.on('click', async e => {
      e.preventDefault();
      $refreshButton.prop('disabled', true);
      try {
        await refetchSchedule();
      } finally {
        $refreshButton.prop('disabled', false);
      }
      applyLobbySpecificFields();
    });

    const clearForNextEntry = () => {
      fieldsToClearOnSuccess.forEach(name => {
        $('#create-battle3-' + name).val('');
      });
      resetRadioButtonGroup('result');
      applyLobbySpecificFields();
      $('#create-battle3-kill_or_assist').trigger('focus');
    };

    const submitBattle = async payload => {
      const response = await window.fetch(config.apiUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Authorization: 'Bearer ' + (config.apiKey || '')
        },
        body: JSON.stringify(payload)
      });
      if (response.ok) {
        showAlert('alert-success', i18n.success);
        clearForNextEntry();
        return;
      }
      let detail = '';
      try {
        const json = await response.json();
        detail = formatErrorDetail(json && json.error);
      } catch (_e) {
        // response had no JSON body; ignore
      }
      let message = i18n.error;
      if (detail) {
        message += ' (' + detail + ')';
      }
      showAlert('alert-danger', message);
    };

    $form.on('submit', async e => {
      e.preventDefault();
      hideAlert();
      $submit.prop('disabled', true);
      try {
        await submitBattle(buildPayload());
      } catch (_e) {
        showAlert('alert-danger', i18n.error);
      } finally {
        $submit.prop('disabled', false);
      }
    });
  });
})(window, jQuery);

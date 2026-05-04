/*! Copyright (C) 2026 AIZAWA Hina | MIT License */

((window, $) => {
  $(() => {
    const config = window.__createBattle3Config || {}
    const i18n = config.i18n || {}
    const $form = $('#create-battle3-form')
    if (!$form.length) {
      return
    }

    const $alert = $('#create-battle3-alert')
    const $submit = $('#create-battle3-submit')

    const fieldsToClearOnSuccess = [
      'kill_or_assist',
      'assist',
      'death',
      'special',
      'inked'
    ]

    const generateUuid = () => {
      if (window.crypto && typeof window.crypto.randomUUID === 'function') {
        return window.crypto.randomUUID()
      }
      const bytes = new Uint8Array(16)
      window.crypto.getRandomValues(bytes)
      bytes[6] = (bytes[6] & 0x0f) | 0x40
      bytes[8] = (bytes[8] & 0x3f) | 0x80
      const hex = Array.from(bytes, b => b.toString(16).padStart(2, '0')).join('')
      return [
        hex.slice(0, 8),
        hex.slice(8, 12),
        hex.slice(12, 16),
        hex.slice(16, 20),
        hex.slice(20)
      ].join('-')
    }

    const showAlert = (klass, message) => {
      $alert
        .removeClass('alert-success alert-danger')
        .addClass(klass)
        .text(message)
        .show()
    }
    const hideAlert = () => {
      $alert.hide()
    }

    const stringValueOf = name => {
      const $el = $('#create-battle3-' + name)
      return ($el.val() || '').toString().trim()
    }
    const intValueOf = name => {
      const v = stringValueOf(name)
      if (v === '') return null
      const n = parseInt(v, 10)
      return Number.isFinite(n) ? n : null
    }
    const checkedRadioValueOf = name => {
      const $el = $form.find('input[type="radio"][name="' + name + '"]:checked')
      return $el.length ? ($el.val() || '').toString() : ''
    }

    const buildPayload = () => {
      const data = {
        uuid: generateUuid(),
        agent: config.agent,
        agent_version: config.agentVersion,
        automated: 'no'
      }

      const selectFields = ['lobby', 'rule', 'stage', 'weapon']
      selectFields.forEach(name => {
        const v = stringValueOf(name)
        if (v !== '') data[name] = v
      })

      const result = checkedRadioValueOf('result')
      if (result !== '') data.result = result

      const koa = intValueOf('kill_or_assist')
      const assist = intValueOf('assist')
      if (koa !== null) data.kill_or_assist = koa
      if (assist !== null) data.assist = assist
      if (koa !== null && assist !== null) {
        data.kill = Math.max(0, koa - assist)
      }

      const intFields = ['death', 'special', 'inked']
      intFields.forEach(name => {
        const v = intValueOf(name)
        if (v !== null) data[name] = v
      })

      return data
    }

    const formatErrorDetail = error => {
      if (!error) return ''
      if (typeof error === 'string') return error
      try {
        return Object.entries(error).map(([k, v]) =>
          k + ': ' + (Array.isArray(v) ? v.join(', ') : String(v))
        ).join(' / ')
      } catch (_e) {
        return ''
      }
    }

    const updateResultButtonStyles = () => {
      const $radios = $form.find('input[type="radio"][name="result"]')
      $radios.each(function () {
        const $label = $(this).closest('label')
        if (this.checked) {
          $label.removeClass('btn-default').addClass('btn-primary')
        } else {
          $label.removeClass('btn-primary').addClass('btn-default')
        }
      })
    }

    const resetResultButtonGroup = () => {
      const $radios = $form.find('input[type="radio"][name="result"]')
      $radios.prop('checked', false)
      $radios.closest('label').removeClass('active')
      const $defaultRadio = $radios.filter('[value=""]')
      $defaultRadio.prop('checked', true)
      $defaultRadio.closest('label').addClass('active')
      updateResultButtonStyles()
    }

    $form.on('change', 'input[type="radio"][name="result"]', updateResultButtonStyles)

    const clearForNextEntry = () => {
      fieldsToClearOnSuccess.forEach(name => {
        $('#create-battle3-' + name).val('')
      })
      resetResultButtonGroup()
      $('#create-battle3-kill_or_assist').trigger('focus')
    }

    const submitBattle = async payload => {
      const response = await window.fetch(config.apiUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Authorization: 'Bearer ' + (config.apiKey || '')
        },
        body: JSON.stringify(payload)
      })
      if (response.ok) {
        showAlert('alert-success', i18n.success)
        clearForNextEntry()
        return
      }
      let detail = ''
      try {
        const json = await response.json()
        detail = formatErrorDetail(json && json.error)
      } catch (_e) {
        // response had no JSON body; ignore
      }
      let message = i18n.error
      if (detail) {
        message += ' (' + detail + ')'
      }
      showAlert('alert-danger', message)
    }

    $form.on('submit', async e => {
      e.preventDefault()
      hideAlert()
      $submit.prop('disabled', true)
      try {
        await submitBattle(buildPayload())
      } catch (_e) {
        showAlert('alert-danger', i18n.error)
      } finally {
        $submit.prop('disabled', false)
      }
    })
  })
})(window, jQuery)

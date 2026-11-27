/*! Copyright (C) 2015-2026 AIZAWA Hina | MIT License */

import {
  type MatchExtended,
  MatcherBaseClass,
  type MatchOptions,
  type Options,
  type OptionsDictionary,
  type OptionsGraph,
  type TranslationKeys,
  ZxcvbnFactory,
  type ZxcvbnResult
} from '@zxcvbn-ts/core';
import { matcherPwnedFactory } from '@zxcvbn-ts/matcher-pwned';
import classes from './password-strength.module.css';

// The language packages are loaded as separate scripts (see PasswordStrengthAsset)
// and register themselves to window.zxcvbnts
interface LanguagePackage {
  adjacencyGraphs?: OptionsGraph;
  dictionary: OptionsDictionary;
  translations?: TranslationKeys;
}

declare global {
  interface Window {
    zxcvbnts?: Record<string, LanguagePackage>;
  }
}

interface Checker {
  check: (password: string) => ZxcvbnResult;
  checkWithPwned: (password: string) => Promise<ZxcvbnResult>;
}

const MAX_SCORE = 4;

// The pwned matcher is asynchronous and asks the Have I Been Pwned API.
// To avoid leaking the password while it is being typed, it is gated: it only
// runs for `pwnedTarget`, which is set while a whole password is checked.
// Comparing with the target also keeps it from running for the sub-tokens that
// the repeat matcher checks recursively.
let pwnedTarget: string | null = null;

function createChecker (translationsName: string): Checker {
  const packages = window.zxcvbnts ?? {};
  const common = packages['language-common'];
  const languages = Object.entries(packages)
    .filter(([name]) => name.startsWith('language-') && name !== 'language-common')
    .map(([, pkg]) => pkg);
  const translations = (packages[translationsName] ?? packages['language-en'])?.translations;

  const pwned = matcherPwnedFactory(window.fetch.bind(window));
  const PwnedMatching = pwned.Matching;
  class GatedPwnedMatching extends MatcherBaseClass {
    private readonly pwnedMatching: MatcherBaseClass;

    constructor (options: Options) {
      super(options);
      this.pwnedMatching = new PwnedMatching(options);
    }

    match (options: MatchOptions): MatchExtended[] | Promise<MatchExtended[]> {
      return options.password === pwnedTarget ? this.pwnedMatching.match(options) : [];
    }
  }

  const factory = new ZxcvbnFactory(
    {
      translations,
      graphs: common?.adjacencyGraphs,
      dictionary: Object.assign(
        {},
        common?.dictionary,
        ...languages.map(lang => lang.dictionary)
      )
    },
    {
      pwned: { ...pwned, Matching: GatedPwnedMatching }
    }
  );

  return {
    check: password => factory.check(password),
    checkWithPwned: password => {
      // The matchers are called synchronously by checkAsync(), so the gate can be
      // closed right after the call.
      pwnedTarget = password;
      try {
        return factory.checkAsync(password);
      } finally {
        pwnedTarget = null;
      }
    }
  };
}

const checkers = new Map<string, Checker>();
function getChecker (translationsName: string): Checker {
  let checker = checkers.get(translationsName);
  if (!checker) {
    checker = createChecker(translationsName);
    checkers.set(translationsName, checker);
  }
  return checker;
}

function createElement (tagName: string, className: string): HTMLElement {
  const element = document.createElement(tagName);
  element.className = className;
  return element;
}

function render (bar: HTMLElement, feedback: HTMLElement, result: ZxcvbnResult | null): void {
  const score = result?.score ?? 0;
  bar.style.width = `${1 + (99 * score / MAX_SCORE)}%`;
  bar.dataset.score = String(score);

  feedback.replaceChildren();
  if (!result) {
    return;
  }

  const { warning, suggestions } = result.feedback;
  if (warning) {
    const p = createElement('p', `${classes.warning} text-danger`);
    p.textContent = warning;
    feedback.append(p);
  }

  if (suggestions.length > 0) {
    const ul = createElement('ul', classes.suggestions);
    ul.append(
      ...suggestions.map(text => {
        const li = document.createElement('li');
        li.textContent = text;
        return li;
      })
    );
    feedback.append(ul);
  }
}

function setup (container: HTMLElement): void {
  const selector = container.dataset.input;
  const input = selector ? document.querySelector<HTMLInputElement>(selector) : null;
  if (!input) {
    return;
  }

  const translationsName = container.dataset.translations ?? 'language-en';
  const gauge = createElement('div', classes.gauge);
  const bar = createElement('div', classes.bar);
  const feedback = createElement('div', `${classes.feedback} help-block`);
  feedback.setAttribute('aria-live', 'polite');
  gauge.append(bar);
  container.classList.add(classes.root);
  container.replaceChildren(gauge, feedback);

  let timerId: number | null = null;
  let pwnedChecked: { password: string; result: ZxcvbnResult } | null = null;

  const update = (): void => {
    timerId = null;
    const password = input.value;
    if (password === '') {
      render(bar, feedback, null);
      return;
    }

    render(
      bar,
      feedback,
      pwnedChecked?.password === password
        ? pwnedChecked.result
        : getChecker(translationsName).check(password)
    );
  };

  const updateWithPwned = (): void => {
    const password = input.value;
    if (password === '' || pwnedChecked?.password === password) {
      return;
    }

    getChecker(translationsName)
      .checkWithPwned(password)
      .then(result => {
        pwnedChecked = { password, result };
        // The password may have been changed while the API was being asked
        if (input.value === password) {
          render(bar, feedback, result);
        }
      })
      .catch(err => {
        console.error(err);
      });
  };

  input.addEventListener('input', () => {
    if (timerId !== null) {
      window.clearTimeout(timerId);
    }
    timerId = window.setTimeout(update, 100);
  });
  input.addEventListener('change', updateWithPwned);

  window.setTimeout(update, 1);
}

function setupAll (): void {
  document.querySelectorAll<HTMLElement>('.password-strength').forEach(setup);
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', setupAll);
} else {
  setupAll();
}

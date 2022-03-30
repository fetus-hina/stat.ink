<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\web;

use IntlDateFormatter;
use Yii;
use app\components\helpers\T;
use app\components\helpers\UserLanguage;
use app\components\helpers\UserTimeZone;
use app\components\i18n\MachineTranslateHelper;
use yii\i18n\MessageSource;
use yii\i18n\MissingTranslationEvent;
use yii\web\Application as Base;
use yii\web\Cookie;

use const PHP_INT_MAX;

/**
 * @property-read bool $isEnabledMachineTranslation
 * @property-read string|null $localeCalendar
 */
final class Application extends Base
{
    public const COOKIE_MACHINE_TRANSLATION = 'language-machine-translation';

    private ?string $locale = null;

    public function init()
    {
        parent::init();
        $this->initLanguage();
        $this->initTimezone();

        if ($this->getIsEnabledMachineTranslation()) {
            $lang = substr(Yii::$app->language, 0, 2);
            if ($lang !== 'ja' && $lang !== 'en') {
                $i18n = Yii::$app->i18n;

                foreach ($i18n->translations as $category => $msgSource) {
                    $handler = function (MissingTranslationEvent $event): void {
                        if (
                            $event->category === null ||
                            $event->message === null ||
                            $event->language === null
                        ) {
                            return;
                        }

                        $result = MachineTranslateHelper::translate(
                            $event->category,
                            $event->message,
                            $event->language
                        );
                        if (is_string($result)) {
                            $event->translatedMessage = $result;
                        }
                    };

                    if (is_array($msgSource)) {
                        $i18n->translations[$category]['on missingTranslation'] = $handler;
                    } else {
                        $msgSource->on(MessageSource::EVENT_MISSING_TRANSLATION, $handler);
                    }
                }
            }
        }
    }

    public function setLocale(string $locale): self
    {
        $atPos = strpos($locale, '@');
        $additional = $atPos === false
            ? ''
            : substr($locale, $atPos + 1);
        $this->locale = $locale;
        Yii::$app->language = substr($locale, 0, $atPos === false ? PHP_INT_MAX : $atPos);
        Yii::$app->formatter->locale = rtrim(implode('@', [
            str_replace('-', '_', Yii::$app->language),
            $additional,
        ]), '@');
        Yii::$app->formatter->calendar = strpos($additional, 'calendar=') !== false
            ? IntlDateFormatter::TRADITIONAL
            : IntlDateFormatter::GREGORIAN;
        $sep = $this->getNumericSeparators();
        Yii::$app->formatter->decimalSeparator = $sep['decimal'];
        Yii::$app->formatter->thousandSeparator = $sep['thousand'];

        Yii::info('Language/Locale updated: ' . implode(', ', [
            'app.language=' . Yii::$app->language,
            'app.locale=' . $this->locale,
            'fmt.locale=' . Yii::$app->formatter->locale,
            'fmt.calendar=' . (
                Yii::$app->formatter->calendar === IntlDateFormatter::TRADITIONAL
                    ? 'TRADITIONAL'
                    : 'GREGORIAN'
            ),
            'fmt.numeric=' . vsprintf('1%s234%s5', [
                Yii::$app->formatter->thousandSeparator,
                Yii::$app->formatter->decimalSeparator,
            ]),
        ]), __METHOD__);

        return $this;
    }

    public function getLocale(): string
    {
        return $this->locale ?: Yii::$app->language;
    }

    public function getLocaleCalendar(): ?string
    {
        if (strpos($this->locale, 'calendar=') !== false) {
            if (preg_match('/^.*?calendar=(\w+).*$/', $this->locale, $match)) {
                return $match[1];
            }
        }

        return null;
    }

    protected function initLanguage(): void
    {
        $lang = UserLanguage::guess();
        if ($lang) {
            $app = T::webApplication(Yii::$app);

            $app->language = $lang->getLanguageId();
            $app->setLocale($lang->lang);
            $app->response->cookies->add(new Cookie([
                'expire' => time() + 86400 * 366,
                'httpOnly' => true,
                'name' => UserLanguage::COOKIE_KEY,
                'sameSite' => Cookie::SAME_SITE_LAX,
                'value' => $lang->lang,
            ]));
        }
    }

    protected function initTimezone(): void
    {
        $tz = UserTimeZone::guess();
        if ($tz) {
            Yii::$app->setTimeZone($tz->identifier);
            Yii::$app->formatter->timeZone = $tz->identifier;
            Yii::$app->response->cookies->add(new Cookie([
                'expire' => time() + 86400 * 366,
                'httpOnly' => true,
                'name' => UserTimeZone::COOKIE_KEY,
                'sameSite' => Cookie::SAME_SITE_LAX,
                'value' => $tz->identifier,
            ]));
        }
    }

    /** @return array<string, string> */
    private function getNumericSeparators(): array
    {
        if (extension_loaded('intl')) {
            $v = Yii::$app->formatter->asDecimal(1234.5, 1);
            if (preg_match('/^1(.)?234(.)5$/', $v, $match)) {
                return [
                    'decimal' => $match[2],
                    'thousand' => $match[1],
                ];
            }
        }

        return [
            'decimal' => '.',
            'thousand' => ',',
        ];
    }

    private ?bool $isEnabledMT = null;

    public function setEnabledMachineTranslation(bool $enabled): void
    {
        $this->isEnabledMT = $enabled;

        $this->response->cookies->add(
            new Cookie([
                'expire' => time() + 86400 * 366,
                'httpOnly' => true,
                'name' => static::COOKIE_MACHINE_TRANSLATION,
                'sameSite' => Cookie::SAME_SITE_LAX,
                'value' => $enabled ? 'enabled' : 'disabled',
            ])
        );
    }

    public function getIsEnabledMachineTranslation(bool $defaultValue = true): bool
    {
        if ($this->isEnabledMT === null) {
            $cookie = Yii::$app->request->cookies->get(static::COOKIE_MACHINE_TRANSLATION);
            if (!$cookie) {
                $this->isEnabledMT = $defaultValue;
            } else {
                $this->isEnabledMT = $cookie->value === 'enabled';
            }
        }

        return $this->isEnabledMT;
    }
}

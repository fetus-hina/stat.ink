<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

declare(strict_types=1);

namespace app\components\web;

use IntlDateFormatter;
use Yii;
use app\components\helpers\UserLanguage;
use app\components\helpers\UserTimeZone;
use yii\web\Application as Base;
use yii\web\Cookie;

class Application extends Base
{
    private $locale = null;
    private $region = 'jp';

    public function init()
    {
        parent::init();
        $this->initLanguage();
        $this->initTimezone();
    }

    public function setLocale(string $locale): self
    {
        $atPos = strpos($locale, '@');
        $additional = ($atPos === false)
            ? ''
            : substr($locale, $atPos + 1);
        $this->locale = $locale;
        Yii::$app->language = substr($locale, 0, $atPos === false ? PHP_INT_MAX : $atPos);
        Yii::$app->formatter->locale = rtrim(implode('@', [
            str_replace('-', '_', Yii::$app->language),
            $additional,
        ]), '@');
        Yii::$app->formatter->calendar = (strpos($additional, 'calendar=') !== false)
            ? IntlDateFormatter::TRADITIONAL
            : IntlDateFormatter::GREGORIAN;
        
        Yii::info('Language/Locale updated: ' . implode(', ', [
            'app.language=' . Yii::$app->language,
            'app.locale=' . $this->locale,
            'fmt.locale=' . Yii::$app->formatter->locale,
            'fmt.calendar=' . (
                Yii::$app->formatter->calendar === IntlDateFormatter::TRADITIONAL
                    ? 'TRADITIONAL'
                    : 'GREGORIAN'
            ),
        ]), __METHOD__);

        return $this;
    }

    public function getLocale(): string
    {
        return $this->locale ?: Yii::$app->language;
    }

    public function setSplatoonRegion($region)
    {
        $this->region = $region;
        return $this;
    }

    public function getSplatoonRegion()
    {
        return $this->region;
    }

    protected function initLanguage(): void
    {
        $lang = UserLanguage::guess();
        if ($lang) {
            Yii::$app->language = $lang->getLanguageId();
            Yii::$app->setLocale($lang->lang);
            Yii::$app->response->cookies->add(new Cookie([
                'name' => UserLanguage::COOKIE_KEY,
                'value' => $lang->lang,
                'expire' => time() + 86400 * 366,
            ]));
        }
    }

    protected function initTimezone(): void
    {
        $tz = UserTimeZone::guess();
        if ($tz) {
            Yii::$app->setTimeZone($tz->identifier);
            Yii::$app->formatter->timeZone = $tz->identifier;
            Yii::$app->setSplatoonRegion($tz->region_id);
            Yii::$app->response->cookies->add(new Cookie([
                'name' => UserTimeZone::COOKIE_KEY,
                'value' => $tz->identifier,
                'expire' => time() + 86400 * 366,
            ]));
        }
    }
}

<?php

/**
 * @copyright Copyright (C) 2018-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components;

use GeoIp2\Database\Reader;
use GeoIp2\Model\City;
use GeoIp2\Model\Country;
use Throwable;
use Yii;
use yii\base\Component;

use function file_exists;
use function substr;

final class GeoIP extends Component
{
    public $dbCity = '@geoip/GeoLite2-City.mmdb';
    public $dbCountry = '@geoip/GeoLite2-Country.mmdb';

    public function city(
        string $ipAddress,
        array $locales = ['en'],
    ): ?City {
        Yii::beginProfile(__METHOD__, __METHOD__);
        try {
            if (!$reader = $this->getReader($this->dbCity, $locales)) {
                return null;
            }

            return $reader->city($ipAddress);
        } finally {
            Yii::endProfile(__METHOD__, __METHOD__);
        }
    }

    public function country(
        string $ipAddress,
        array $locales = ['en'],
    ): ?Country {
        Yii::beginProfile(__METHOD__, __METHOD__);
        try {
            if (!$reader = $this->getReader($this->dbCountry, $locales)) {
                return null;
            }

            return $reader->country($ipAddress);
        } finally {
            Yii::endProfile(__METHOD__, __METHOD__);
        }
    }

    protected function getReader(string $dbPathAlias, array $locales): ?Reader
    {
        Yii::beginProfile(__METHOD__, __METHOD__);
        try {
            $dbPath = (string)Yii::getAlias($dbPathAlias);
            if (!@file_exists($dbPath)) {
                return null;
            }

            try {
                return new Reader($dbPath, $locales);
            } catch (Throwable $e) {
                return null;
            }
        } finally {
            Yii::endProfile(__METHOD__, __METHOD__);
        }
    }

    public function getLang(?string $appLang = null): string
    {
        if ($appLang === null) {
            $appLang = Yii::$app->language;
        }

        return match (substr($appLang, 0, 2)) {
            'de', 'en', 'es', 'fr', 'ja', 'ru' => substr($appLang, 0, 2),
            'pt' => 'pt-BR',
            'zh' => $appLang === 'zh-CN' ? 'zh-CN' : 'zh-TW',
            default => 'en',
        };
    }
}

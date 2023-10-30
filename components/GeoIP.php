<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
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

class GeoIP extends Component
{
    public $dbCity = '@geoip/GeoLite2-City.mmdb';
    public $dbCountry = '@geoip/GeoLite2-Country.mmdb';

    public function city(
        string $ipAddress,
        array $locales = ['en'],
    ): ?City {
        if (!$reader = $this->getReader($this->dbCity, $locales)) {
            return null;
        }

        return $reader->city($ipAddress);
    }

    public function country(
        string $ipAddress,
        array $locales = ['en'],
    ): ?Country {
        if (!$reader = $this->getReader($this->dbCountry, $locales)) {
            return null;
        }

        return $reader->country($ipAddress);
    }

    protected function getReader(string $dbPathAlias, array $locales): ?Reader
    {
        $dbPath = Yii::getAlias($dbPathAlias);
        if (!@file_exists($dbPath)) {
            return null;
        }

        try {
            return new Reader($dbPath, $locales);
        } catch (Throwable $e) {
            return null;
        }
    }

    public function getLang(?string $appLang = null): string
    {
        if ($appLang === null) {
            $appLang = Yii::$app->language;
        }
        switch (substr($appLang, 0, 2)) {
            case 'de':
            case 'en':
            case 'es':
            case 'fr':
            case 'ja':
            case 'ru':
                return substr($appLang, 0, 2);

            case 'zh':
                return $appLang === 'zh-CN'
                    ? 'zh-CN'
                    : 'zh-TW';

            case 'pt':
                return 'pt-BR';

            default:
                return 'en';
        }
    }
}

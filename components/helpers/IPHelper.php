<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use Yii;

use const DNS_A;
use const DNS_AAAA;

class IPHelper
{
    public static function getLocationByIP(
        ?string $ipAddr = null,
        ?string $language = null
    ): ?string {
        $ipAddr = $ipAddr ?: static::getCurrentIP();
        $geoIP = Yii::$app->geoip;
        $geoLang = $geoIP->getLang($language);
        $get = function ($obj) use ($geoLang): ?string {
            if (!$obj) {
                return null;
            }
            return $obj->names[$geoLang] ?? $obj->name;
        };

        $city = $geoIP->city($ipAddr);
        if (!$city) {
            return null;
        }

        $info = array_filter([
            $get($city->city),
            $get($city->mostSpecificSubdivision),
            $get($city->country),
        ]);
        if (!$info) {
            return null;
        }

        return implode(', ', $info);
    }

    public static function reverseLookup(?string $ipAddr): ?string
    {
        $ipAddr = $ipAddr ?: static::getCurrentIP();

        $hostName = @gethostbyaddr($ipAddr);
        if (!$hostName) {
            return null;
        }

        $ipAddrList = static::queryIpAddressListByName($hostName);
        if (!in_array($ipAddr, $ipAddrList, true)) {
            return null;
        }

        return $hostName;
    }

    // gethostbynamel for both ipv4 and ipv6
    protected static function queryIpAddressListByName(string $hostName): array
    {
        $tasks = [
            [ DNS_A, 'ip' ],
            [ DNS_AAAA, 'ipv6' ],
        ];
        $results = [];
        foreach ($tasks as $task) {
            if ($records = @dns_get_record($hostName, $task[0])) {
                $results = array_merge(
                    $results,
                    array_column($records, $task[1])
                );
            }
        }

        return $results;
    }

    protected static function getCurrentIP(): ?string
    {
        $ipAddr = Yii::$app->getRequest()->getUserIP();
        return $ipAddr ?: null;
    }
}

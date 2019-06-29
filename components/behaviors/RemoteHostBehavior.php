<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\behaviors;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;

class RemoteHostBehavior extends AttributeBehavior
{
    public $attributes = [
        ActiveRecord::EVENT_BEFORE_INSERT => [ 'remote_host' ],
    ];

    protected function getValue($event)
    {
        $ipAddr = Yii::$app->getRequest()->getUserIP();
        if (!$ipAddr) {
            return null;
        }

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
}

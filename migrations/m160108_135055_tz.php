<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\Region;
use yii\db\Migration;

class m160108_135055_tz extends Migration
{
    public function safeUp()
    {
        $regionNA = Region::findOne(['key' => 'na'])->id;
        $this->batchInsert(
            'timezone',
            ['identifier', 'name', 'order', 'region_id'],
            [
                [ 'America/St_Johns', 'North America (Newfoundland)', 10001, $regionNA ],
                [ 'America/Halifax', 'North America (AT)', 10002, $regionNA ],
                [ 'America/Regina', 'North America (Saskatchewan)', 10003, $regionNA ],
            ],
        );

        $newOrder = [
            'America/St_Johns',
            'America/Halifax',
            'America/New_York',
            'America/Chicago',
            'America/Regina',
            'America/Denver',
            'America/Phoenix',
            'America/Los_Angeles',
            'America/Anchorage',
            'America/Adak',
            'Pacific/Honolulu',
        ];
        $order = 20 + count($newOrder);
        foreach (array_reverse($newOrder) as $ident) {
            $this->update('timezone', ['order' => $order--], ['identifier' => $ident]);
        }
    }

    public function safeDown()
    {
        $this->delete('timezone', [
            'identifier' => [
                'America/St_Johns',
                'America/Halifax',
                'America/Regina',
            ],
        ]);

        // order は戻さなくていいよね
    }
}

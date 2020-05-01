<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

class m200501_084522_north_korea extends Migration
{
    public function safeUp()
    {
        $krID = (int)(new Query())->select('id')->from('country')->where(['key' => 'kr'])->scalar();
        $this->update('country', ['name' => 'South Korea (ROK)'], ['id' => $krID]);
        $this->update('timezone', ['name' => 'South Korea (ROK)'], ['identifier' => 'Asia/Seoul']);

        $this->insert('country', ['key' => 'kp', 'name' => 'North Korea (DPRK)']);
        $kpID = (int)$this->db->lastInsertID;

        $region = (int)(new Query())->select('id')->from('region')->where(['key' => 'jp'])->scalar();
        $tzGroup = (int)(new Query())
            ->select('id')
            ->from('timezone_group')
            ->where(['name' => 'East Asia'])
            ->scalar();
        $this->insert('timezone', [
            'identifier' => 'Asia/Pyongyang',
            'name' => 'North Korea (DPRK)',
            'order' => 0x7fffffff,
            'region_id' => $region,
            'group_id' => $tzGroup,
        ]);
        $pyongyang = (int)$this->db->lastInsertID;
        $this->insert('timezone_country', ['timezone_id' => $pyongyang, 'country_id' => $kpID]);

        $list = [
            'Asia/Tokyo' => 1,
            'Asia/Seoul' => 2,
            'Asia/Pyongyang' => 3,
            'Asia/Shanghai' => 4,
            'Asia/Urumqi' => 5,
            'Asia/Taipei' => 6,
            'Asia/Ulaanbaatar' => 7,
            'Asia/Hovd' => 8,
        ];
        foreach ($list as $tz => $order) {
            $this->update('timezone', ['order' => 0x70000000 + $order], ['identifier' => $tz]);
        }
        foreach ($list as $tz => $order) {
            $this->update('timezone', ['order' => $order], ['identifier' => $tz]);
        }
    }

    public function safeDown()
    {
        $krID = (int)(new Query())->select('id')->from('country')->where(['key' => 'kr'])->scalar();
        $kpID = (int)(new Query())->select('id')->from('country')->where(['key' => 'kp'])->scalar();

        $this->delete('timezone_country', ['country_id' => $kpID]);
        $this->delete('timezone', ['identifier' => 'Asia/Pyongyang']);
        $this->delete('country', ['id' => $kpID]);

        $this->update('timezone', ['name' => 'Korea'], ['identifier' => 'Asia/Seoul']);
        $this->update('country', ['name' => 'Korea'], ['id' => $krID]);
    }
}

<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Expression;
use yii\db\Query;

class m200408_205810_guam extends Migration
{
    public function safeUp()
    {
        $micronesia = (new Query())
            ->select('id')
            ->from('timezone_group')
            ->where(['timezone_group.name' => 'Micronesia'])
            ->scalar();

        $saipanOrder = (new Query())
            ->select('[[order]]')
            ->from('timezone')
            ->where(['timezone.identifier' => 'Pacific/Saipan'])
            ->scalar();

        // 北マリアナ諸島を1つ後ろにずらす
        $this->update(
            'timezone',
            ['order' => $saipanOrder + 1],
            ['timezone.identifier' => 'Pacific/Saipan'],
        );

        // グアムの移籍
        $guam = (new Query())
            ->select('id')
            ->from('timezone')
            ->where(['timezone.identifier' => 'Pacific/Guam'])
            ->scalar();

        $this->update(
            'timezone',
            [
                'group_id' => $micronesia,
                'order' => $saipanOrder,
            ],
            ['id' => $guam],
        );

        $this->insert('country', ['key' => 'gu', 'name' => 'Guam']);
        $this->delete('timezone_country', ['timezone_id' => $guam]);
        $this->insert('timezone_country', [
            'timezone_id' => $guam,
            'country_id' => (new Query())
                ->select('id')
                ->from('country')
                ->where(['key' => 'gu'])
                ->scalar(),
        ]);
    }

    public function safeDown()
    {
        // めんどくさいので down は未サポート
        return false;
    }
}

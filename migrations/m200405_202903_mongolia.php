<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

class m200405_202903_mongolia extends Migration
{
    public function safeUp()
    {
        $region = (new Query())
            ->select('id')
            ->from('region')
            ->where(['key' => 'jp'])
            ->scalar();

        $tzGroup = (new Query())
            ->select('id')
            ->from('timezone_group')
            ->where(['name' => 'East Asia'])
            ->scalar();

        $order = (new Query())
            ->select('MAX([[order]])')
            ->from('timezone')
            ->where(['group_id' => $tzGroup])
            ->scalar();

        $this->batchInsert('timezone', ['identifier', 'name', 'order', 'region_id', 'group_id'], [
            [
                'Asia/Ulaanbaatar',
                'Mongolia',
                (int)$order + 1,
                (int)$region,
                (int)$tzGroup,
            ],
            [
                'Asia/Hovd',
                'Mongolia (West)',
                (int)$order + 2,
                (int)$region,
                (int)$tzGroup,
            ],
        ]);

        $this->insert('country', ['key' => 'mn', 'name' => 'Mongolia']);

        $country = (new Query())
            ->select('id')
            ->from('country')
            ->where(['key' => 'mn'])
            ->scalar();

        $this->batchInsert('timezone_country', ['timezone_id', 'country_id'], array_map(
            function (array $row) use ($country): array {
                return [(int)$row['id'], (int)$country];
            },
            (new Query())
                ->select('*')
                ->from('timezone')
                ->where(['identifier' => ['Asia/Ulaanbaatar', 'Asia/Hovd']])
                ->all(),
        ));
    }

    public function safeDown()
    {
        $country = (new Query())
            ->select('id')
            ->from('country')
            ->where(['key' => 'mn'])
            ->scalar();

        $this->delete('timezone_country', ['country_id' => (int)$country]);
        $this->delete('timezone', ['identifier' => ['Asia/Ulaanbaatar', 'Asia/Hovd']]);
        $this->delete('country', ['id' => (int)$country]);
    }
}

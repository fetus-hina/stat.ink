<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use yii\db\Query;

class m170922_201548_russian_time extends Migration
{
    public function safeUp()
    {
        $this->insert('country', ['key' => 'ru', 'name' => 'Russia']);
        $russia = $this->getRussiaId();
        $euRegion = (new Query())
            ->select(['id'])
            ->from('region')
            ->where(['key' => 'eu'])
            ->limit(1)
            ->scalar();
        $this->batchInsert('timezone', ['identifier', 'name', 'order', 'region_id'], [
            [ 'Europe/Kaliningrad', 'Russia Time (Kaliningrad)',   51, $euRegion ],
            [ 'Europe/Moscow',      'Russia Time (Moscow)',        52, $euRegion ],
            [ 'Europe/Samara',      'Russia Time (Samara)',        53, $euRegion ],
            [ 'Asia/Yekaterinburg', 'Russia Time (Yekaterinburg)', 54, $euRegion ],
            [ 'Asia/Omsk',          'Russia Time (Omsk)',          55, $euRegion ],
            [ 'Asia/Krasnoyarsk',   'Russia Time (Krasnoyarsk)',   56, $euRegion ],
            [ 'Asia/Irkutsk',       'Russia Time (Irkutsk)',       57, $euRegion ],
            [ 'Asia/Yakutsk',       'Russia Time (Yakutsk)',       58, $euRegion ],
            [ 'Asia/Vladivostok',   'Russia Time (Vladivostok)',   59, $euRegion ],
            [ 'Asia/Magadan',       'Russia Time (Magadan)',       60, $euRegion ],
            [ 'Asia/Kamchatka',     'Russia Time (Kamchatka)',     61, $euRegion ],
        ]);
        $this->batchInsert(
            'timezone_country',
            ['timezone_id', 'country_id'],
            array_map(
                function (array $row) use ($russia): array {
                    return [
                        $row['id'],
                        $russia,
                    ];
                },
                (new Query())
                    ->select(['id'])
                    ->from('timezone')
                    ->where(['identifier' => [
                        'Europe/Kaliningrad',
                        'Europe/Moscow',
                        'Europe/Samara',
                        'Asia/Yekaterinburg',
                        'Asia/Omsk',
                        'Asia/Krasnoyarsk',
                        'Asia/Irkutsk',
                        'Asia/Yakutsk',
                        'Asia/Vladivostok',
                        'Asia/Magadan',
                        'Asia/Kamchatka',
                    ]])
                    ->orderBy(['id' => SORT_ASC])
                    ->all(),
            ),
        );
    }

    public function safeDown()
    {
        $russia = $this->getRussiaId();
        $this->delete('timezone_country', ['country_id' => $russia]);
        $this->delete('timezone', ['identifier' => [
            'Europe/Kaliningrad',
            'Europe/Moscow',
            'Europe/Samara',
            'Asia/Yekaterinburg',
            'Asia/Omsk',
            'Asia/Krasnoyarsk',
            'Asia/Irkutsk',
            'Asia/Yakutsk',
            'Asia/Vladivostok',
            'Asia/Magadan',
            'Asia/Kamchatka',
        ]]);
        $this->delete('country', ['id' => $russia]);
    }

    private function getRussiaId(): int
    {
        return (new \yii\db\Query())
            ->select(['id'])
            ->from('country')
            ->where(['key' => 'ru'])
            ->limit(1)
            ->scalar();
    }
}

<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class m200409_163412_melanesia extends Migration
{
    public function safeUp()
    {
        $tzGroupId = $this->upTzGroup();
        $regionId = $this->getRegionId();
        $tldIdMap = $this->upCountries();
        $order = 10 * ceil(
            (1 + (int)(new Query())->select('MAX([[order]])')->from('timezone')->scalar()) / 10,
        );
        foreach ($this->getData() as $cctld => $info) {
            foreach ($info['tz'] as $tzIdStr => $tzName) {
                $this->insert('timezone', [
                    'identifier' => $tzIdStr,
                    'name' => $tzName,
                    'order' => $order++,
                    'region_id' => $regionId,
                    'group_id' => $tzGroupId,
                ]);
                $this->insert('timezone_country', [
                    'timezone_id' => (int)(new Query())
                        ->select('id')
                        ->from('timezone')
                        ->where(['identifier' => $tzIdStr])
                        ->scalar(),
                    'country_id' => $tldIdMap[$cctld],
                ]);
            }
        }
    }

    public function safeDown()
    {
        $data = $this->getData();

        $tzIdentifiers = ArrayHelper::toFlatten(array_map(
            function (array $info): array {
                return array_keys($info['tz']);
            },
            array_values($data),
        ));

        $tzIds = (new Query())
            ->select('id')
            ->from('timezone')
            ->where(['identifier' => $tzIdentifiers])
            ->column();

        $countryIds = (new Query())
            ->select('id')
            ->from('country')
            ->where(['key' => array_keys($data)])
            ->column();

        $this->delete('timezone_country', ['or',
            ['timezone_id' => $tzIds],
            ['country_id' => $countryIds],
        ]);

        $this->delete('timezone', ['id' => $tzIds]);
        $this->delete('country', ['id' => $countryIds]);
        $this->delete('timezone_group', ['id' => $this->getTzGroupId()]);

        $this->alterColumn('timezone', 'name', $this->string(32)->notNull());
    }

    private function upTzGroup(): int
    {
        $this->insert('timezone_group', [
            'order' => 21,
            'name' => 'Melanesia',
        ]);

        return $this->getTzGroupId();
    }

    private function getTzGroupId(): int
    {
        return (int)(new Query())
            ->select('id')
            ->from('timezone_group')
            ->where(['name' => 'Melanesia'])
            ->scalar();
    }

    private function getRegionId(): int
    {
        return (int)(new Query())
            ->select('id')
            ->from('region')
            ->where(['key' => 'eu'])
            ->scalar();
    }

    private function upCountries(): array
    {
        $data = $this->getData();
        $this->batchInsert('country', ['key', 'name'], array_map(
            function (string $cctld, array $cInfo): array {
                return [$cctld, $cInfo['name']];
            },
            array_keys($data),
            array_values($data),
        ));

        return ArrayHelper::map(
            (new Query())->select('*')->from('country')->where(['key' => array_keys($data)])->all(),
            'key',
            'id',
        );
    }

    private function getData(): array
    {
        return [
            // 主権国家等
            'fj' => [
                'name' => 'Fiji',
                'tz' => [
                    'Pacific/Fiji' => 'Fiji',
                ],
            ],
            'pg' => [
                'name' => 'Papua New Guinea',
                'tz' => [
                    'Pacific/Port_Moresby' => 'Papua New Guinea',
                    'Pacific/Bougainville' => 'Papua New Guinea (Bougainville)',
                ],
            ],
            'sb' => [
                'name' => 'Solomon Islands',
                'tz' => [
                    'Pacific/Guadalcanal' => 'Solomon Islands',
                ],
            ],
            'vu' => [
                'name' => 'Vanuatu',
                'tz' => [
                    'Pacific/Efate' => 'Vanuatu',
                ],
            ],
            // 各国領
            'fc' => [
                'name' => 'New Caledonia',
                'tz' => [
                    'Pacific/Noumea' => 'New Caledonia',
                ],
            ],
        ];
    }
}

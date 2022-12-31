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

class m200405_160149_west_asia extends Migration
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
            fn (array $info): array => array_keys($info['tz']),
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
    }

    private function upTzGroup(): int
    {
        $this->insert('timezone_group', [
            'order' => 14,
            'name' => 'Western Asia',
        ]);

        return $this->getTzGroupId();
    }

    private function getTzGroupId(): int
    {
        return (int)(new Query())
            ->select('id')
            ->from('timezone_group')
            ->where(['name' => 'Western Asia'])
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
            fn (string $cctld, array $cInfo): array => [$cctld, $cInfo['name']],
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
            'am' => [
                'name' => 'Armenia',
                'tz' => [
                    'Asia/Yerevan' => 'Armenia',
                ],
            ],
            'az' => [
                'name' => 'Azerbaijan',
                'tz' => [
                    'Asia/Baku' => 'Azerbaijan',
                ],
            ],
            'bh' => [
                'name' => 'Bahrain',
                'tz' => [
                    'Asia/Bahrain' => 'Bahrain',
                ],
            ],
            'cy' => [
                'name' => 'Cyprus',
                'tz' => [
                    'Asia/Nicosia' => 'Cyprus',
                ],
            ],
            'ge' => [
                'name' => 'Georgia',
                'tz' => [
                    'Asia/Tbilisi' => 'Georgia',
                ],
            ],
            'ir' => [
                'name' => 'Iran',
                'tz' => [
                    'Asia/Tehran' => 'Iran',
                ],
            ],
            'iq' => [
                'name' => 'Iraq',
                'tz' => [
                    'Asia/Baghdad' => 'Iraq',
                ],
            ],
            'il' => [
                'name' => 'Israel',
                'tz' => [
                    'Asia/Jerusalem' => 'Israel',
                ],
            ],
            'jo' => [
                'name' => 'Jordan',
                'tz' => [
                    'Asia/Amman' => 'Jordan',
                ],
            ],
            'kw' => [
                'name' => 'Kuwait',
                'tz' => [
                    'Asia/Kuwait' => 'Kuwait',
                ],
            ],
            'lb' => [
                'name' => 'Lebanon',
                'tz' => [
                    'Asia/Beirut' => 'Lebanon',
                ],
            ],
            'om' => [
                'name' => 'Oman',
                'tz' => [
                    'Asia/Muscat' => 'Oman',
                ],
            ],
            'ps' => [
                'name' => 'Palestine',
                'tz' => [
                    'Asia/Gaza' => 'Palestine',
                ],
            ],
            'qa' => [
                'name' => 'Qatar',
                'tz' => [
                    'Asia/Qatar' => 'Qatar',
                ],
            ],
            'sa' => [
                'name' => 'Saudi Arabia',
                'tz' => [
                    'Asia/Riyadh' => 'Saudi Arabia',
                ],
            ],
            'sy' => [
                'name' => 'Syria',
                'tz' => [
                    'Asia/Damascus' => 'Syria',
                ],
            ],
            'tr' => [
                'name' => 'Turkey',
                'tz' => [
                    'Europe/Istanbul' => 'Turkey',
                ],
            ],
            'ae' => [
                'name' => 'United Arab Emirates',
                'tz' => [
                    'Asia/Dubai' => 'United Arab Emirates',
                ],
            ],
            'ye' => [
                'name' => 'Yemen',
                'tz' => [
                    'Asia/Aden' => 'Yemen',
                ],
            ],
        ];
    }
}

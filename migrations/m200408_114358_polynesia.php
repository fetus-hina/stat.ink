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

class m200408_114358_polynesia extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('timezone', 'name', $this->string(64)->notNull());

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

        $this->alterColumn('timezone', 'name', $this->string(32)->notNull());
    }

    private function upTzGroup(): int
    {
        $this->insert('timezone_group', [
            'order' => 22,
            'name' => 'Polynesia',
        ]);

        return $this->getTzGroupId();
    }

    private function getTzGroupId(): int
    {
        return (int)(new Query())
            ->select('id')
            ->from('timezone_group')
            ->where(['name' => 'Polynesia'])
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
            // 主権国家等
            'ck' => [
                'name' => 'Cook Islands',
                'tz' => [
                    'Pacific/Rarotonga' => 'Cook Islands',
                ],
            ],
            'ki' => [
                'name' => 'Kiribati',
                'tz' => [
                    'Pacific/Kiritimati' => 'Kiribati (Kiritimati)',
                    'Pacific/Enderbury' => 'Kiribati (Enderbury)',
                    'Pacific/Tarawa' => 'Kiribati (Tarawa)',
                ],
            ],
            'nz' => [
                'name' => 'New Zealand',
                'tz' => [
                    'Pacific/Auckland' => 'New Zealand',
                    'Pacific/Chatham' => 'New Zealand (Chatham)',
                ],
            ],
            'nu' => [
                'name' => 'Niue',
                'tz' => [
                    'Pacific/Niue' => 'Niue',
                ],
            ],
            'ws' => [
                'name' => 'Samoa',
                'tz' => [
                    'Pacific/Apia' => 'Samoa',
                ],
            ],
            'to' => [
                'name' => 'Tonga',
                'tz' => [
                    'Pacific/Tongatapu' => 'Tonga',
                ],
            ],
            'tv' => [
                'name' => 'Tuvalu',
                'tz' => [
                    'Pacific/Funafuti' => 'Tuvalu',
                ],
            ],
            // 各国領
            'as' => [
                'name' => 'American Samoa',
                'tz' => [
                    'Pacific/Pago_Pago' => 'American Samoa',
                ],
            ],
            'pf' => [
                'name' => 'French Polynesia',
                'tz' => [
                    'Pacific/Gambier' => 'French Polynesia (Gambier Islands)',
                    'Pacific/Marquesas' => 'French Polynesia (Marquesas Islands)',
                    'Pacific/Tahiti' => 'French Polynesia (Tahiti)',
                ],
            ],
            'pn' => [
                'name' => 'Pitcairn Islands',
                'tz' => [
                    'Pacific/Pitcairn' => 'Pitcairn Islands',
                ],
            ],
            'tk' => [
                'name' => 'Tokelau',
                'tz' => [
                    'Pacific/Fakaofo' => 'Tokelau',
                ],
            ],
            'um' => [
                'name' => 'US Minor Outlying Islands',
                'tz' => [
                    'Pacific/Wake' => 'Wake Island',
                    'Pacific/Midway' => 'Midway Island',
                ],
            ],
            'wf' => [
                'name' => 'Wallis and Futuna',
                'tz' => [
                    'Pacific/Wallis' => 'Wallis and Futuna',
                ],
            ],
        ];
    }
}

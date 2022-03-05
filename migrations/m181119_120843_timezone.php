<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

class m181119_120843_timezone extends Migration
{
    public function safeUp()
    {
        foreach ($this->namesTable() as $ident => $upd) {
            $this->update('timezone', ['name' => $upd[0]], ['identifier' => $ident]);
        }
        $this->batchInsert(
            'timezone',
            ['identifier', 'name', 'order', 'region_id', 'group_id'],
            [
                ['Asia/Seoul', 'Korea', 2, $this->region('jp'), $this->group('East Asia')],
                ['Asia/Taipei', 'Taiwan', 5, $this->region('jp'), $this->group('East Asia')],
            ]
        );
        $this->batchInsert(
            'timezone_country',
            ['timezone_id', 'country_id'],
            [
                [$this->timezone('Asia/Seoul'), $this->country('kr')],
                [$this->timezone('Asia/Taipei'), $this->country('tw')],
            ]
        );
        $this->delete('timezone_country', ['and',
            ['timezone_id' => $this->timezone('Asia/Tokyo')],
            ['country_id' => $this->country('kr')],
        ]);
        $this->delete('timezone_country', ['and',
            ['timezone_id' => $this->timezone('Asia/Shanghai')],
            ['country_id' => $this->country('tw')],
        ]);
    }

    public function safeDown()
    {
        $this->delete(
            'timezone_country',
            [
                'timezone_id' => [
                    $this->timezone('Asia/Seoul'),
                    $this->timezone('Asia/Taipei'),
                ],
            ]
        );
        $this->delete('timezone', ['identifier' => ['Asia/Seoul', 'Asia/Taipei']]);
        foreach ($this->namesTable() as $ident => $upd) {
            $this->update(
                'timezone',
                ['name' => $upd[1]],
                ['identifier' => $ident],
            );
        }

        // Restore for "Japan & Korea time"
        $this->insert('timezone_country', [
            'timezone_id' => $this->timezone('Asia/Tokyo'),
            'country_id' => $this->country('kr'),
        ]);

        // Restore for "China & Taiwan time"
        $this->insert('timezone_country', [
            'timezone_id' => $this->timezone('Asia/Shanghai'),
            'country_id' => $this->country('tw'),
        ]);
    }

    public function namesTable(): array
    {
        return [
            'Asia/Tokyo' => ['Japan', 'Japan & Korea Time'],
            'Asia/Shanghai' => ['China (PRC)', 'China & Taiwan Time'],
            'Asia/Urumqi' => ['Xinjiang', 'Xinjiang Time'],

            'Europe/Athens' => ['Europe (East)', 'European Time (East)'],
            'Europe/Paris' => ['Europe (Central)', 'European Time (Central)'],
            'Europe/London' => ['Europe (West)', 'European Time (West)'],

            'Pacific/Honolulu' => ['Hawaii', 'Hawaii Time'],
            'Pacific/Guam' => ['Guam', 'Guam Time'],

            'Europe/Kaliningrad' => ['Russia (Kaliningrad)', 'Russia Time (Kaliningrad)'],
            'Europe/Moscow' => ['Russia (Moscow)', 'Russia Time (Moscow)'],
            'Europe/Samara' => ['Russia (Samara)', 'Russia Time (Samara)'],
            'Asia/Yekaterinburg' => ['Russia (Yekaterinburg)','Russia Time (Yekaterinburg)'],
            'Asia/Omsk' => ['Russia (Omsk)', 'Russia Time (Omsk)'],
            'Asia/Krasnoyarsk' => ['Russia (Krasnoyarsk)', 'Russia Time (Krasnoyarsk)'],
            'Asia/Irkutsk' => ['Russia (Irkutsk)', 'Russia Time (Irkutsk)'],
            'Asia/Yakutsk' => ['Russia (Yakutsk)', 'Russia Time (Yakutsk)'],
            'Asia/Vladivostok' => ['Russia (Vladivostok)', 'Russia Time (Vladivostok)'],
            'Asia/Magadan' => ['Russia (Magadan)', 'Russia Time (Magadan)'],
            'Asia/Kamchatka' => ['Russia (Kamchatka)', 'Russia Time (Kamchatka)'],
        ];
    }

    private function timezone(string $key): int
    {
        return $this->getIdByKey('timezone', $key, 'identifier');
    }

    private function country(string $key): int
    {
        return $this->getIdByKey('country', $key);
    }

    private function region(string $key): int
    {
        return $this->getIdByKey('region', $key);
    }

    private function group(string $name): int
    {
        return $this->getIdByKey('timezone_group', $name, 'name');
    }

    private function getIdByKey(
        string $table,
        string $value,
        string $key = 'key',
        string $id = 'id'
    ): int {
        $ret = (new Query())
            ->select(['id' => $id])
            ->from($table)
            ->where([$key => $value])
            ->limit(1)
            ->scalar();
        if ($ret === null) {
            throw new Exception('Could not find ID');
        }
        return (int)$ret;
    }
}

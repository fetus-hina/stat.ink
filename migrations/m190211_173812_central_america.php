<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

class m190211_173812_central_america extends Migration
{
    public function safeUp()
    {
        return $this->upCountries() &&
            $this->upTimezones();
    }

    public function safeDown()
    {
        return $this->downTimezones() &&
            $this->downCountries();
    }

    private function upCountries(): bool
    {
        $data = $this->getCountries();
        $this->batchInsert(
            'country',
            ['key', 'name'],
            array_map(
                fn (string $cctld, array $data): array => [$cctld, $data['name']],
                array_keys($data),
                array_values($data),
            ),
        );
        return true;
    }

    private function downCountries(): bool
    {
        $this->delete('country', ['key' => array_keys($this->getCountries())]);
        return true;
    }

    private function upTimezones(): bool
    {
        $regionId = $this->queryId('region', ['key' => 'na']);
        $groupId = $this->queryId('timezone_group', ['name' => 'Central America']);
        $order = 62;
        foreach ($this->getCountries() as $cctld => $data) {
            $countryId = $this->queryId('country', ['key' => $cctld]);
            foreach ($data['timezones'] as $tzId => $tzName) {
                $this->insert('timezone', [
                    'identifier' => $tzId,
                    'name' => $tzName,
                    'order' => $order++,
                    'region_id' => $regionId,
                    'group_id' => $groupId,
                ]);
                $id = $this->queryId('timezone', ['identifier' => $tzId]);
                $this->insert('timezone_country', [
                    'timezone_id' => $id,
                    'country_id' => $countryId,
                ]);
            }
        }
        return true;
    }

    private function downTimezones(): bool
    {
        $tzList = [];
        foreach ($this->getCountries() as $data) {
            foreach (array_keys($data['timezones']) as $tz) {
                $tzList[] = $tz;
            }
        }

        $list = array_map(
            fn (array $data): int => (int)$data['id'],
            (new Query())
                ->select('id')
                ->from('timezone')
                ->where(['identifier' => $tzList])
                ->all(),
        );
        $this->delete('timezone_country', ['timezone_id' => $list]);
        $this->delete('timezone', ['id' => $list]);
        return true;
    }

    private function getCountries(): array
    {
        return [
            'bz' => [
                'name' => 'Belize',
                'timezones' => [
                    'America/Belize' => 'Belize',
                ],
            ],
            'cr' => [
                'name' => 'Costa Rica',
                'timezones' => [
                    'America/Costa_Rica' => 'Costa Rica',
                ],
            ],
            'sv' => [
                'name' => 'El Salvador',
                'timezones' => [
                    'America/El_Salvador' => 'El Salvador',
                ],
            ],
            'gt' => [
                'name' => 'Guatemala',
                'timezones' => [
                    'America/Guatemala' => 'Guatemala',
                ],
            ],
            'hn' => [
                'name' => 'Honduras',
                'timezones' => [
                    'America/Tegucigalpa' => 'Honduras',
                ],
            ],
            'ni' => [
                'name' => 'Nicaragua',
                'timezones' => [
                    'America/Managua' => 'Nicaragua',
                ],
            ],
            'pa' => [
                'name' => 'Panama',
                'timezones' => [
                    'America/Panama' => 'Panama',
                ],
            ],
        ];
    }

    private function queryId(string $table, array $where): int
    {
        $query = (new Query())
            ->select(['id'])
            ->from($table)
            ->where($where)
            ->limit(1);
        $value = $query->scalar();
        $value = filter_var($value, FILTER_VALIDATE_INT);
        if ($value === false) {
            throw new Exception(vsprintf('Query Error at %s:%d, query=%s', [
                __FILE__,
                __LINE__,
                $query->createCommand()->rawSql,
            ]));
        }
        return $value;
    }
}

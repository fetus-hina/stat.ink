<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Connection;
use yii\db\Query;

class m190211_165543_mx_timezones extends Migration
{
    public function safeUp()
    {
        $db = $this->db;
        assert($db instanceof Connection);

        $this->insert('country', [
            'key' => 'mx',
            'name' => 'Mexico',
        ]);
        $countryId = $this->queryId('country', ['key' => 'mx']);

        $regionId = $this->queryId('region', ['key' => 'na']);
        $groupId = $this->queryId('timezone_group', ['name' => 'North America']);
        $order = 33;
        foreach ($this->getData() as $ident => $name) {
            $this->insert('timezone', [
                'identifier' => $ident,
                'name' => $name,
                'order' => $order++,
                'region_id' => $regionId,
                'group_id' => $groupId,
            ]);
            $id = $db->lastInsertID;
            $this->insert('timezone_country', [
                'timezone_id' => $id,
                'country_id' => $countryId,
            ]);
        }
    }

    public function safeDown()
    {
        foreach (array_keys($this->getData()) as $ident) {
            $id = $this->queryId('timezone', ['identifier' => $ident]);
            $this->delete('timezone_country', ['timezone_id' => $id]);
            $this->delete('timezone', ['id' => $id]);
        }
        $this->delete('country', ['key' => 'mx']);
    }

    private function getData(): array
    {
        return [
            'America/Cancun' => 'Mexico (East)',
            'America/Mexico_City' => 'Mexico (Central)',
            'America/Mazatlan' => 'Mexico (Pacific)',
            'America/Hermosillo' => 'Mexico (Sonora)',
            'America/Tijuana' => 'Mexico (Northwest)',
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
            throw new \Exception(vsprintf('Query Error at %s:%d, query=%s', [
                __FILE__,
                __LINE__,
                $query->createCommand()->rawSql,
            ]));
        }
        return $value;
    }
}

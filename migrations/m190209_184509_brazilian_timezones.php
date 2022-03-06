<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

class m190209_184509_brazilian_timezones extends Migration
{
    public function safeUp()
    {
        $this->insert('country', ['key' => 'br', 'name' => 'Brazil']);
        $countryId = $this->queryId('country', ['key' => 'br']);
        $regionId = $this->queryId('region', ['key' => 'na']);
        $groupId = $this->queryId('timezone_group', ['name' => 'South America']);
        $timeZones = [
            'America/Noronha' => 'Brazil (Fernando de Noronha)',
            'America/Sao_Paulo' => 'Brazil (Brasília)',
            'America/Fortaleza' => 'Brazil (Northeastern)',
            'America/Cuiaba' => 'Brazil (Mato Grosso)',
            'America/Manaus' => 'Brazil (Eastern Amazonas)',
            'America/Eirunepe' => 'Brazil (Weastern Amazonas)',
        ];
        $order = 71;
        foreach ($timeZones as $identifier => $name) {
            $this->insert('timezone', [
                'identifier' => $identifier,
                'name' => $name,
                'order' => $order++,
                'region_id' => $regionId,
                'group_id' => $groupId,
            ]);
            $tzId = $this->queryId('timezone', ['identifier' => $identifier]);
            $this->insert('timezone_country', [
                'timezone_id' => $tzId,
                'country_id' => $countryId,
            ]);
        }
    }

    public function safeDown()
    {
        $countryId = $this->queryId('country', ['key' => 'br']);
        $this->delete('timezone_country', ['country_id' => $countryId]);
        $this->delete('timezone', [
            'identifier' => [
                'America/Noronha',
                'America/Sao_Paulo',
                'America/Fortaleza',
                'America/Cuiaba',
                'America/Manaus',
                'America/Eirunepe',
            ],
        ]);
        $this->delete('country', ['id' => $countryId]);
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

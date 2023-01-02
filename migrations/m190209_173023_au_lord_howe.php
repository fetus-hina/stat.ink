<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

class m190209_173023_au_lord_howe extends Migration
{
    public function safeUp()
    {
        $this->insert('timezone', [
            'identifier' => 'Australia/Lord_Howe',
            'name' => 'Lord Howe Island',
            'order' => 40,
            'region_id' => $this->queryId('region', ['key' => 'eu']), // mmm
            'group_id' => $this->queryId('timezone_group', ['name' => 'Australia/Oceania']),
        ]);
        $this->insert('timezone_country', [
            'timezone_id' => $this->queryId('timezone', ['identifier' => 'Australia/Lord_Howe']),
            'country_id' => $this->queryId('country', ['key' => 'au']),
        ]);
    }

    public function safeDown()
    {
        $id = $this->queryId('timezone', ['identifier' => 'Australia/Lord_Howe']);
        $this->delete('timezone_country', ['timezone_id' => $id]);
        $this->delete('timezone', ['id' => $id]);
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

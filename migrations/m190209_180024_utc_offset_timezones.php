<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

class m190209_180024_utc_offset_timezones extends Migration
{
    public function safeUp()
    {
        $dummyRegionId = $this->queryId('region', ['key' => 'eu']);
        $groupEtc = $this->queryId('timezone_group', ['name' => 'Others']);
        $order = 100;
        $data = array_filter(array_map(
            function (int $offset) use (&$order, $dummyRegionId, $groupEtc): ?array {
                if ($offset === 0) {
                    return null;
                }

                return [
                    sprintf('Etc/GMT%+d', -$offset),
                    sprintf('UTC%+d', $offset),
                    $order++,
                    $dummyRegionId,
                    $groupEtc,
                ];
            },
            range(+14, -12, -1),
        ));
        $this->batchInsert(
            'timezone',
            ['identifier', 'name', 'order', 'region_id', 'group_id'],
            $data,
        );
    }

    public function safeDown()
    {
        $this->delete('timezone', "identifier LIKE 'Etc/GMT%'");
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

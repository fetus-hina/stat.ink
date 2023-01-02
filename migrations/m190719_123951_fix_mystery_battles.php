<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m190719_123951_fix_mystery_battles extends Migration
{
    public function safeUp()
    {
        $startPeriod = (int)floor(strtotime('2019-07-18T21:00:00+09:00') / 7200) - 1;

        // あらかじめ WHERE で絞り込んだ INDEX を作って
        // この INDEX を優先してもらうことで高速化を図る
        // これを作成しないと、シーケンシャルスキャンでとんでもないことになった…
        $index = 'tmp_battle2_' . hash('crc32b', __METHOD__);
        $this->execute(
            "CREATE UNIQUE INDEX {$index} ON {{battle2}}([[id]]) " .
            'WHERE ([[map_id]] IS NULL) ' .
            "AND ([[period]] >= {$startPeriod})",
        );

        // 現在、battle2_splatnet テーブルの中身が、テキストになっているので、
        // テキストを一度 JSONB として解釈してもらってさらにクエリを投げる。
        // 死ぬほど遅い。
        $db = Yii::$app->db;
        $this->execute(
            'UPDATE {{battle2}} ' .
            'SET [[map_id]] = {{map2}}.[[id]] ' .
            'FROM {{battle2_splatnet}} {{j}}, {{map2}} ' .
            'WHERE ((' . implode(') AND (', [
                '{{battle2}}.[[id]] = {{j}}.[[id]]',
                "((({{j}}.[[json]]->>0)::JSONB)->'stage'->>'id')::INTEGER = {{map2}}.[[splatnet]]",
                'JSONB_TYPEOF({{j}}.[[json]]) = ' . $db->quoteValue('string'),
                '{{battle2}}.[[map_id]] IS NULL',
                '{{battle2}}.[[period]] >= ' . $db->quoteValue($startPeriod),
            ]) . '))',
        );

        $this->execute("DROP INDEX {$index}");
    }

    public function safeDown()
    {
    }
}

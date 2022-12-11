<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Connection;
use yii\db\Query;

/**
 * data fixer for #1167
 * See commit c31715897f7d366deac62d8a93fe43443f5566af
 */
final class m221211_213913_fix_1167 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $db = $this->db;
        assert($db instanceof Connection);

        $tmpTblSelect = (new Query())
            ->select([
                'salmon_id' => '{{%salmon_wave3}}.[[salmon_id]]',
                'valid_count' => 'SUM({{%salmon_wave3}}.[[golden_delivered]])',
            ])
            ->from('{{%salmon_wave3}}')
            ->innerJoin('{{%salmon3}}', '{{%salmon_wave3}}.[[salmon_id]] = {{%salmon3}}.[[id]]')
            ->andWhere(['and',
                ['BETWEEN', '{{%salmon_wave3}}.[[wave]]', 1, 3],
                ['{{%salmon3}}.[[is_deleted]]' => false],
            ])
            ->groupBy(['{{%salmon_wave3}}.[[salmon_id]]'])
            ->andHaving(['and',
                // 納品数が NULL やマイナスになっている変なデータがない
                'SUM(CASE WHEN {{%salmon_wave3}}.[[golden_delivered]] IS NULL THEN 1 ELSE 0 END) = 0',
                'SUM(CASE WHEN {{%salmon_wave3}}.[[golden_delivered]] < 0 THEN 1 ELSE 0 END) = 0',
                // 現に格納されている数字が間違っている
                'SUM({{%salmon_wave3}}.[[golden_delivered]]) <> MAX({{%salmon3}}.[[golden_eggs]])',
            ]);
        $this->execute(
            vsprintf('CREATE TEMPORARY TABLE %s AS %s', [
                $db->quoteTableName('fix_1167'),
                $tmpTblSelect->createCommand($db)->rawSql,
            ]),
        );

        $this->execute(
            'UPDATE {{%salmon3}} ' .
            'SET [[golden_eggs]] = {{fix_1167}}.[[valid_count]] ' .
            'FROM {{fix_1167}} ' .
            'WHERE {{%salmon3}}.[[id]] = {{fix_1167}}.[[salmon_id]]'
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        return true;
    }

    protected function vacuumTables(): array
    {
        return [
            '{{%salmon3}}',
        ];
    }
}

<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

final class m240831_165541_stat_salmon3_map_king extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%stat_salmon3_map_king}}', [
            'map_id' => $this->pkRef('{{%map3}}')->null(),
            'big_map_id' => $this->pkRef('{{%bigrun_map3}}')->null(),
            'king_id' => $this->pkRef('{{%salmon_king3}}')->notNull(),
            'jobs' => $this->bigInteger()->notNull(),
            'cleared' => $this->bigInteger()->notNull(),

            vsprintf('CHECK ((%s) OR (%s))', [
                '[[map_id]] IS NULL AND [[big_map_id]] IS NOT NULL',
                '[[map_id]] IS NOT NULL AND [[big_map_id]] IS NULL',
            ]),
            'UNIQUE ([[map_id]], [[big_map_id]], [[king_id]])',
        ]);

        $select = (new Query())
            ->select([
                'map_id' => '{{%salmon3}}.[[stage_id]]',
                'big_map_id' => '{{%salmon3}}.[[big_stage_id]]',
                'king_id' => '{{%salmon3}}.[[king_salmonid_id]]',
                'jobs' => 'COUNT(*)',
                'cleared' => 'SUM(CASE WHEN {{%salmon3}}.[[clear_extra]] THEN 1 ELSE 0 END)',
            ])
            ->from('{{%salmon3}}')
            ->andWhere(['and',
                [
                    '{{%salmon3}}.[[clear_waves]]' => 3,
                    '{{%salmon3}}.[[has_broken_data]]' => false,
                    '{{%salmon3}}.[[has_disconnect]]' => false,
                    '{{%salmon3}}.[[is_automated]]' => true,
                    '{{%salmon3}}.[[is_deleted]]' => false,
                    '{{%salmon3}}.[[is_eggstra_work]]' => false,
                    '{{%salmon3}}.[[is_private]]' => false,
                ],
                ['not', ['{{%salmon3}}.[[clear_extra]]' => null]],
                ['not', ['{{%salmon3}}.[[king_salmonid_id]]' => null]],
                ['or',
                    ['and',
                        ['not', ['{{%salmon3}}.[[stage_id]]' => null]],
                        ['{{%salmon3}}.[[big_stage_id]]' => null],
                        ['{{%salmon3}}.[[is_big_run]]' => false],
                    ],
                    ['and',
                        ['not', ['{{%salmon3}}.[[big_stage_id]]' => null]],
                        ['{{%salmon3}}.[[stage_id]]' => null],
                        ['{{%salmon3}}.[[is_big_run]]' => true],
                    ],
                ],
            ])
            ->groupBy([
                '{{%salmon3}}.[[stage_id]]',
                '{{%salmon3}}.[[big_stage_id]]',
                '{{%salmon3}}.[[king_salmonid_id]]',
            ]);

        $this->execute(
            vsprintf('INSERT INTO %s ( %s ) %s', [
                '{{%stat_salmon3_map_king}}',
                implode(', ', array_map(
                    $this->db->quoteColumnName(...),
                    array_keys($select->select),
                )),
                $select->createCommand()->rawSql,
            ]),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%stat_salmon3_map_king}}');

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%stat_salmon3_map_king}}',
        ];
    }
}

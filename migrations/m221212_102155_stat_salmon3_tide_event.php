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

final class m221212_102155_stat_salmon3_tide_event extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%stat_salmon3_tide_event}}', [
            'stage_id' => $this->pkRef('{{%salmon_map3}}')->null(),
            'big_stage_id' => $this->pkRef('{{%map3}}')->null(),
            'tide_id' => $this->pkRef('{{%salmon_water_level2}}')->notNull(),
            'event_id' => $this->pkRef('{{%salmon_event3}}')->null(),
            'jobs' => $this->bigInteger()->notNull(),
            'cleared' => $this->bigInteger()->notNull(),
        ]);

        $db = $this->db;
        assert($db instanceof Connection);
        $select = (new Query())
            ->select([
                'stage_id' => '{{%salmon3}}.[[stage_id]]',
                'big_stage_id' => '{{%salmon3}}.[[big_stage_id]]',
                'tide_id' => '{{%salmon_wave3}}.[[tide_id]]',
                'event_id' => '{{%salmon_wave3}}.[[event_id]]',
                'jobs' => 'COUNT(*)',
                'cleared' => vsprintf('SUM(CASE %s END)', [
                    implode(' ', [
                        'WHEN {{%salmon3}}.[[clear_waves]] >= {{%salmon_wave3}}.[[wave]] THEN 1',
                        'ELSE 0',
                    ]),
                ]),
            ])
            ->from('{{%salmon3}}')
            ->innerJoin('{{%salmon_wave3}}', '{{%salmon3}}.[[id]] = {{%salmon_wave3}}.[[salmon_id]]')
            ->andWhere([
                '{{%salmon3}}.[[has_broken_data]]' => false,
                '{{%salmon3}}.[[has_disconnect]]' => false,
                '{{%salmon3}}.[[is_automated]]' => true,
                '{{%salmon3}}.[[is_deleted]]' => false,
                '{{%salmon3}}.[[is_private]]' => false,
            ])
            ->andWhere(['not', ['{{%salmon_wave3}}.[[tide_id]]' => null]])
            ->andWhere(['BETWEEN', '{{%salmon3}}.[[clear_waves]]', 0, 3])
            ->andWhere(['or',
                ['and',
                    '{{%salmon3}}.[[stage_id]] IS NOT NULL',
                    '{{%salmon3}}.[[big_stage_id]] IS NULL',
                ],
                ['and',
                    '{{%salmon3}}.[[stage_id]] IS NULL',
                    '{{%salmon3}}.[[big_stage_id]] IS NOT NULL',
                ],
            ])
            ->groupBy([
                '{{%salmon3}}.[[stage_id]]',
                '{{%salmon3}}.[[big_stage_id]]',
                '{{%salmon_wave3}}.[[tide_id]]',
                '{{%salmon_wave3}}.[[event_id]]',
            ])
            ->createCommand($db)
            ->rawSql;

        $this->execute(
            vsprintf('INSERT INTO %s %s', [
                $db->quoteTableName('{{%stat_salmon3_tide_event}}'),
                $select,
            ]),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%stat_salmon3_tide_event}}');

        return true;
    }

    protected function vacuumTables(): array
    {
        return [
            '{{%stat_salmon3_tide_event}}',
        ];
    }
}

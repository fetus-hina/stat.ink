<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m240208_113247_knockout3_histogram extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute(
            vsprintf('CREATE FUNCTION %s ( %s ) %s AS $$%s$$', [
                'HISTOGRAM_WIDTH',
                implode(', ', [
                    'IN [[samples]] BIGINT',
                    'IN [[stddev]] NUMERIC',
                    'IN [[width_hint]] INTEGER',
                ]),
                implode(' ', [
                    'RETURNS INTEGER',
                    'LANGUAGE SQL',
                    'IMMUTABLE',
                    'RETURNS NULL ON NULL INPUT',
                    'SECURITY INVOKER',
                ]),
                'SELECT GREATEST(1.0, ROUND((3.49 * $2) / POWER($1, 1.0 / 3.0) / $3::numeric))::integer * $3',
            ]),
        );

        $this->addColumn(
            '{{%knockout3}}',
            'histogram_width',
            (string)$this->integer()->null(),
        );

        $this->createTable('{{%knockout3_histogram}}', [
            'id' => $this->bigPrimaryKey(),
            'season_id' => $this->pkRef('{{%season3}}')->notNull(),
            'rule_id' => $this->pkRef('{{%rule3}}')->notNull(),
            'map_id' => $this->pkRef('{{%map3}}')->null(),
            'class_value' => $this->integer()->notNull(),
            'count' => $this->bigInteger()->notNull(),
        ]);
        $this->createIndex(
            'knockout3_histogram_season_rule_map',
            '{{%knockout3_histogram}}',
            ['season_id', 'rule_id', 'COALESCE([[map_id]], 0)', 'class_value'],
            true,
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%knockout3_histogram}}');
        $this->dropColumn('{{%knockout3}}', 'histogram_width');

        $this->execute(
            'DROP FUNCTION HISTOGRAM_WIDTH ( IN BIGINT, IN NUMERIC, IN INTEGER )',
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%knockout3}}',
            '{{%knockout3_histogram}}',
        ];
    }
}

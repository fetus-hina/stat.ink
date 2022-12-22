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

final class m221221_221629_stat_s3_knockout extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $db = $this->db;
        assert($db instanceof Connection);

        $this->createTable('{{%knockout3}}', [
            'id' => $this->primaryKey(),
            'season_id' => $this->pkRef('{{%season3}}')->notNull(),
            'rule_id' => $this->pkRef('{{%rule3}}')->notNull(),
            'map_id' => $this->pkRef('{{%map3}}')->null(),
            'battles' => $this->bigInteger()->notNull(),
            'knockout' => $this->bigInteger()->notNull(),
            'avg_battle_time' => $this->float()->null(),
            'stddev_battle_time' => $this->float()->null(),
            'avg_knockout_time' => $this->float()->null(),
            'stddev_knockout_time' => $this->float()->null(),
        ]);

        $this->createIndex(
            'knockout3_season_rule_map',
            '{{%knockout3}}',
            ['season_id', 'rule_id', 'COALESCE([[map_id]], 0)'],
            true,
        );

        $time = '(EXTRACT(EPOCH FROM {{%battle3}}.[[end_at]]) - EXTRACT(EPOCH FROM {{%battle3}}.[[start_at]]))';
        $koTime = "(CASE WHEN {{%battle3}}.[[is_knockout]] THEN {$time} ELSE NULL END)";
        $query = (new Query())
            ->select([
                'season_id' => '{{%season3}}.[[id]]',
                'rule_id' => '{{%battle3}}.[[rule_id]]',
                'map_id' => '{{%battle3}}.[[map_id]]',
                'battles' => 'COUNT(*)',
                'knockout' => 'SUM(CASE WHEN {{%battle3}}.[[is_knockout]] THEN 1 ELSE 0 END)',
                'avg_battle_time' => "AVG({$time})",
                'stddev_battle_time' => "STDDEV_SAMP({$time})",
                'avg_knockout_time' => "AVG({$koTime})",
                'stddev_knockout_time' => "STDDEV_SAMP({$koTime})",
            ])
            ->from('{{%battle3}}')
            ->innerJoin('{{%lobby3}}', '{{%battle3}}.[[lobby_id]] = {{%lobby3}}.[[id]]')
            ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
            ->innerJoin('{{%rule3}}', '{{%battle3}}.[[rule_id]] = {{%rule3}}.[[id]]')
            ->innerJoin('{{%season3}}', '{{%battle3}}.[[start_at]] <@ {{%season3}}.[[term]]')
            ->andWhere(['and',
                ['not', ['{{%battle3}}.[[end_at]]' => null]],
                ['not', ['{{%battle3}}.[[is_knockout]]' => null]],
                ['not', ['{{%battle3}}.[[start_at]]' => null]],
                [
                    '{{%battle3}}.[[has_disconnect]]' => false,
                    '{{%battle3}}.[[is_automated]]' => true,
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[use_for_entire]]' => true,
                    '{{%lobby3}}.[[key]]' => 'xmatch',
                    '{{%result3}}.[[aggregatable]]' => true,
                    '{{%rule3}}.[[key]]' => ['area', 'yagura', 'hoko', 'asari'],
                ],
                '{{%battle3}}.[[start_at]] < {{%battle3}}.[[end_at]]',
            ])
            ->groupBy([
                '{{%season3}}.[[id]]',
                '{{%battle3}}.[[rule_id]]',
                '{{%battle3}}.[[map_id]]',
            ])
            ->orderBy([
                '{{%season3}}.[[id]]' => SORT_ASC,
                '{{%battle3}}.[[rule_id]]' => SORT_ASC,
                '{{%battle3}}.[[map_id]]' => SORT_ASC,
            ]);

        $this->execute(
            vsprintf('INSERT INTO %s (%s) %s', [
                $db->quoteTableName('{{%knockout3}}'),
                implode(
                    ', ',
                    array_map(
                        fn (string $columnName) => $db->quoteColumnName($columnName),
                        array_keys($query->select),
                    ),
                ),
                $query->createCommand($db)->rawSql,
            ]),
        );

        $query->select['map_id'] = '(NULL)';
        $query->groupBy(['{{%season3}}.[[id]]', '{{%battle3}}.[[rule_id]]']);
        $query->orderBy([
            '{{%season3}}.[[id]]' => SORT_ASC,
            '{{%battle3}}.[[rule_id]]' => SORT_ASC,
        ]);
        $this->execute(
            vsprintf('INSERT INTO %s (%s) %s', [
                $db->quoteTableName('{{%knockout3}}'),
                implode(
                    ', ',
                    array_map(
                        fn (string $columnName) => $db->quoteColumnName($columnName),
                        array_keys($query->select),
                    ),
                ),
                $query->createCommand($db)->rawSql,
            ]),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%knockout3}}');

        return true;
    }

    protected function vacuumTables(): array
    {
        return [
            '{{%knockout3}}',
        ];
    }
}

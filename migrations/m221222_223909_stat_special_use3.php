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

final class m221222_223909_stat_special_use3 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $db = $this->db;
        assert($db instanceof Connection);

        $this->createTable('{{%stat_special_use3}}', [
            'id' => $this->primaryKey(),
            'season_id' => $this->pkRef('{{%season3}}')->notNull(),
            'rule_id' => $this->pkRef('{{%rule3}}')->null(),
            'special_id' => $this->pkRef('{{%special3}}')->notNull(),
            'sample_size' => $this->bigInteger()->notNull(),
            'win' => $this->bigInteger()->notNull(),
            'avg_uses' => $this->float()->notNull(),
            'stddev' => $this->float()->null(),
            'percentile_5' => $this->integer()->null(),
            'percentile_25' => $this->integer()->null(),
            'percentile_50' => $this->integer()->null(),
            'percentile_75' => $this->integer()->null(),
            'percentile_95' => $this->integer()->null(),
            'percentile_100' => $this->integer()->null(),
        ]);

        $this->createIndex(
            'stat_special_use3_season_rule_special',
            '{{%stat_special_use3}}',
            ['season_id', 'COALESCE([[rule_id]], 0)', 'special_id'],
            true,
        );

        foreach ([false, true] as $aggRule) {
            $select = $this->buildSelect(rule: $aggRule);
            $sql = vsprintf('INSERT INTO %s ( %s ) %s', [
                $db->quoteTableName('{{%stat_special_use3}}'),
                implode(
                    ', ',
                    array_map(
                        fn (string $column): string => $db->quoteColumnName($column),
                        array_keys($select->select),
                    ),
                ),
                $select->createCommand($db)->rawSql,
            ]);

            $this->execute($sql);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%stat_special_use3}}');

        return true;
    }

    protected function vacuumTables(): array
    {
        return [
            '{{%stat_special_use3}}',
        ];
    }

    private function buildSelect(bool $rule): Query
    {
        $percentile = fn (int $pct): string => sprintf(
            'percentile_disc(%.2f) WITHIN GROUP (ORDER BY {{bp}}.[[special]] ASC)',
            $pct / 100,
        );

        return (new Query())
            ->select([
                'season_id' => '{{%season3}}.[[id]]',
                'rule_id' => $rule ? '{{%battle3}}.[[rule_id]]' : '(NULL)',
                'special_id' => '{{%weapon3}}.[[special_id]]',
                'sample_size' => 'COUNT(*)',
                'win' => 'SUM(CASE WHEN {{%result3}}.[[is_win]] = {{bp}}.[[is_our_team]] THEN 1 ELSE 0 END)',
                'avg_uses' => 'AVG({{bp}}.[[special]])',
                'stddev' => 'STDDEV_SAMP({{bp}}.[[special]])',
                'percentile_5' => $percentile(5),
                'percentile_25' => $percentile(25),
                'percentile_50' => $percentile(50),
                'percentile_75' => $percentile(75),
                'percentile_95' => $percentile(95),
                'percentile_100' => 'MAX({{bp}}.[[special]])',
            ])
            ->from('{{%battle3}}')
            ->innerJoin('{{%lobby3}}', '{{%battle3}}.[[lobby_id]] = {{%lobby3}}.[[id]]')
            ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
            ->innerJoin('{{%season3}}', '{{%battle3}}.[[start_at]] <@ {{%season3}}.[[term]]')
            ->innerJoin(['bp' => '{{%battle_player3}}'], '{{%battle3}}.[[id]] = {{bp}}.[[battle_id]]')
            ->innerJoin('{{%weapon3}}', '{{bp}}.[[weapon_id]] = {{%weapon3}}.[[id]]')
            ->innerJoin('{{%special3}}', '{{%weapon3}}.[[special_id]] = {{%special3}}.[[id]]')
            ->andWhere(['and',
                [
                    '{{%battle3}}.[[has_disconnect]]' => false,
                    '{{%battle3}}.[[is_automated]]' => true,
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[use_for_entire]]' => true,
                    '{{%lobby3}}.[[key]]' => 'xmatch',
                    '{{%result3}}.[[aggregatable]]' => true,
                    '{{bp}}.[[is_me]]' => false,
                ],
                ['not', ['{{%battle3}}.[[rule_id]]' => null]],
                ['not', ['{{%weapon3}}.[[special_id]]' => null]],
                ['not', ['{{bp}}.[[special]]' => null]],
            ])
            ->groupBy(
                array_filter([
                    '{{%season3}}.[[id]]',
                    $rule ? '{{%battle3}}.[[rule_id]]' : null,
                    '{{%weapon3}}.[[special_id]]',
                ]),
            );
    }
}

<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Connection;
use yii\db\Query;

final class m230110_105341_stats_kd_win3 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $db = $this->db;
        assert($db instanceof Connection);

        $this->createTable('{{%stat_kd_win_rate3}}', [
            'season_id' => $this->pkRef('{{%season3}}')->notNull(),
            'lobby_id' => $this->pkRef('{{%lobby3}}')->notNull(),
            'rule_id' => $this->pkRef('{{%rule3}}')->notNull(),
            'kills' => $this->integer()->notNull(),
            'deaths' => $this->integer()->notNull(),
            'battles' => $this->bigInteger()->notNull(),
            'wins' => $this->bigInteger()->notNull(),
            'PRIMARY KEY ([[season_id]], [[lobby_id]], [[rule_id]], [[kills]], [[deaths]])',
        ]);

        // the index that excluded lobby_id
        $this->createIndex(
            'stat_kd_win_rate3_season_rule_kill_death',
            '{{%stat_kd_win_rate3}}',
            ['season_id', 'rule_id', 'kills', 'deaths'],
            false,
        );

        $this->execute(
            vsprintf('INSERT INTO %s %s', [
                $db->quoteTableName('{{%stat_kd_win_rate3}}'),
                (new Query())
                    ->select([
                        'season_id' => '{{%season3}}.[[id]]',
                        'lobby_id' => '{{%battle3}}.[[lobby_id]]',
                        'rule_id' => '{{%battle3}}.[[rule_id]]',
                        'kills' => self::limit20('{{%battle_player3}}.[[kill]]'),
                        'deaths' => self::limit20('{{%battle_player3}}.[[death]]'),
                        'battles' => 'COUNT(*)',
                        'wins' => vsprintf('SUM(CASE %s END)', [
                            implode(' ', [
                                'WHEN {{%result3}}.[[is_win]] = {{%battle_player3}}.[[is_our_team]] THEN 1',
                                'ELSE 0',
                            ]),
                        ]),
                    ])
                    ->from('{{%battle3}}')
                    ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
                    ->innerJoin('{{%season3}}', '{{%battle3}}.[[start_at]] <@ {{%season3}}.[[term]]')
                    ->innerJoin('{{%battle_player3}}', '{{%battle3}}.[[id]] = {{%battle_player3}}.[[battle_id]]')
                    ->andWhere(['and',
                        [
                            '{{%battle3}}.[[has_disconnect]]' => false,
                            '{{%battle3}}.[[is_automated]]' => true,
                            '{{%battle3}}.[[is_deleted]]' => false,
                            '{{%battle3}}.[[use_for_entire]]' => true,
                            '{{%result3}}.[[aggregatable]]' => true,
                        ],
                        ['not', ['{{%battle3}}.[[lobby_id]]' => null]],
                        ['not', ['{{%battle3}}.[[lobby_id]]' => $this->key2id('{{%lobby3}}', 'private')]],
                        ['not', ['{{%battle3}}.[[rule_id]]' => null]],
                        ['not', ['{{%battle3}}.[[rule_id]]' => $this->key2id('{{%rule3}}', 'tricolor')]],
                        ['not', ['{{%battle_player3}}.[[death]]' => null]],
                        ['not', ['{{%battle_player3}}.[[kill]]' => null]],
                    ])
                    ->groupBy([
                        '{{%season3}}.[[id]]',
                        '{{%battle3}}.[[lobby_id]]',
                        '{{%battle3}}.[[rule_id]]',
                        self::limit20('{{%battle_player3}}.[[kill]]'),
                        self::limit20('{{%battle_player3}}.[[death]]'),
                    ])
                    ->createCommand($db)
                    ->rawSql,
            ]),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTables(['{{%stat_kd_win_rate3}}']);

        return true;
    }

    protected function vacuumTables(): array
    {
        return [
            '{{%stat_kd_win_rate3}}',
        ];
    }

    private static function limit20(string $column): string
    {
        return vsprintf('(CASE WHEN %1$s > 20 THEN 20 ELSE %1$s END)', [
            $column,
        ]);
    }
}

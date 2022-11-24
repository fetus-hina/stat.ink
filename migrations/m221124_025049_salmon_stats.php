<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

final class m221124_025049_salmon_stats extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%user_stat_salmon3}}', [
            'user_id' => $this->pkRef('{{%user}}')->notNull(),
            'jobs' => $this->bigInteger()->notNull(),
            'agg_jobs' => $this->bigInteger()->notNull(),
            'clear_jobs' => $this->bigInteger()->notNull(),
            'total_waves' => $this->bigInteger()->notNull(), // not includes Extra Wave
            'clear_waves' => $this->bigInteger()->notNull(),
            'king_appearances' => $this->bigInteger()->notNull(),
            'king_defeated' => $this->bigInteger()->notNull(),
            'golden_eggs' => $this->bigInteger()->notNull(),
            'power_eggs' => $this->bigInteger()->notNull(),
            'rescues' => $this->bigInteger()->notNull(),
            'rescued' => $this->bigInteger()->notNull(),
            'peak_danger_rate' => $this->decimal(5, 1)->null(),
            'peak_title_id' => $this->pkRef('{{%salmon_title3}}')->null(),
            'peak_title_exp' => $this->integer()->null(),

            'PRIMARY KEY ([[user_id]])',
        ]);

        $cond = implode(' AND ', [
            '{{%salmon3}}.[[is_private]] = FALSE',
            '{{%salmon3}}.[[danger_rate]] IS NOT NULL',
            '{{%salmon3}}.[[danger_rate]] > 0.0',
            '{{%salmon3}}.[[clear_waves]] IS NOT NULL',
            '{{%salmon3}}.[[title_after_id]] IS NOT NULL',
            '{{%salmon3}}.[[title_exp_after]] IS NOT NULL',
            '{{%salmon_player3}}.[[is_disconnected]] = FALSE',
            '{{%salmon_player3}}.[[golden_eggs]] IS NOT NULL',
            '{{%salmon_player3}}.[[power_eggs]] IS NOT NULL',
            '{{%salmon_player3}}.[[rescue]] IS NOT NULL',
            '{{%salmon_player3}}.[[rescued]] IS NOT NULL',
            vsprintf('((%1$s IS NULL) OR (%1$s IS NOT NULL AND %2$s IS NOT NULL))', [
                '{{%salmon3}}.[[king_salmonid_id]]',
                '{{%salmon3}}.[[clear_extra]]',
            ]),
        ]);
        $condKing = '{{%salmon3}}.[[king_salmonid_id]] IS NOT NULL';

        $agg = fn (?string $additionalCond = null, string $then = '1', string $else = '0') => vsprintf(
            'SUM(CASE WHEN %s AND %s THEN %s ELSE %s END)',
            [
                $cond,
                $additionalCond ?? 'TRUE',
                $then,
                $else,
            ],
        );

        $query = (new Query())
            ->select([
                'user_id' => '{{%salmon3}}.[[user_id]]',
                'jobs' => 'COUNT(*)',
                'agg_jobs' => $agg(),
                'clear_jobs' => $agg('{{%salmon3}}.[[clear_waves]] >= 3'),
                'total_waves' => $agg(null, 'LEAST(3, {{%salmon3}}.[[clear_waves]] + 1)'),
                'clear_waves' => $agg(null, '{{%salmon3}}.[[clear_waves]]'),
                'king_appearances' => $agg($condKing),
                'king_defeated' => $agg("{$condKing} AND {{%salmon3}}.[[clear_extra]] = TRUE"),
                'golden_eggs' => $agg(null, '{{%salmon_player3}}.[[golden_eggs]]'),
                'power_eggs' => $agg(null, '{{%salmon_player3}}.[[power_eggs]]'),
                'rescues' => $agg(null, '{{%salmon_player3}}.[[rescue]]'),
                'rescued' => $agg(null, '{{%salmon_player3}}.[[rescued]]'),
                'peak_danger_rate' => vsprintf('MAX(CASE %s END)', [
                    "WHEN {$cond} AND {{%salmon3}}.[[clear_waves]] >= 3 THEN {{%salmon3}}.[[danger_rate]]",
                    'ELSE NULL',
                ]),
            ])
            ->from('{{%salmon3}}')
            ->innerJoin('{{%salmon_player3}}', implode(' AND ', [
                '{{%salmon3}}.[[id]] = {{%salmon_player3}}.[[salmon_id]]',
                '{{%salmon_player3}}.[[is_me]] = TRUE',
            ]))
            ->andWhere(['{{%salmon3}}.[[is_deleted]]' => false])
            ->groupBy(['{{%salmon3}}.[[user_id]]']);
        $this->execute(
            vsprintf('INSERT INTO {{%%user_stat_salmon3}} (%s) %s', [
                implode(', ', array_keys($query->select)),
                $query->createCommand($this->db)->rawSql,
            ]),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_stat_salmon3}}');

        return true;
    }

    protected function vacuumTables(): array
    {
        return ['{{%user_stat_salmon3}}'];
    }
}

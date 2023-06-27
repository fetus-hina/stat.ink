<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\helpers\TypeHelper;
use yii\db\Connection;
use yii\db\Query;

final class m230627_093321_salmon3_weapon extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%salmon3_user_stats_weapon}}', [
            'user_id' => $this->pkRef('{{user}}')->notNull(),
            'weapon_id' => $this->pkRef('{{%salmon_weapon3}}')->notNull(),
            'total_waves' => $this->integer()->notNull()->defaultValue(0),
            'normal_waves' => $this->integer()->notNull()->defaultValue(0),
            'normal_waves_cleared' => $this->integer()->notNull()->defaultValue(0),
            'xtra_waves' => $this->integer()->notNull()->defaultValue(0),
            'xtra_waves_cleared' => $this->integer()->notNull()->defaultValue(0),

            'PRIMARY KEY ([[user_id]], [[weapon_id]])',
            'CHECK ([[normal_waves]] > 0 OR [[xtra_waves]] > 0)',
            'CHECK ([[normal_waves]] + [[xtra_waves]] = [[total_waves]])',
        ]);
        $this->execute(
            vsprintf('CREATE UNIQUE INDEX %s ON %s (%s)', [
                '[[salmon3_user_stats_weapon_count]]',
                '{{%salmon3_user_stats_weapon}}',
                implode(', ', [
                    '[[user_id]] ASC',
                    '[[total_waves]] DESC',
                    '[[normal_waves]] DESC',
                    '[[normal_waves_cleared]] DESC',
                    '[[xtra_waves_cleared]] DESC',
                    '[[weapon_id]] ASC',
                ]),
            ]),
        );

        $db = TypeHelper::instanceOf($this->db, Connection::class);
        $select = (new Query())
            ->select([
                'user_id' => '{{%salmon3}}.[[user_id]]',
                'weapon_id' => '{{%salmon_player_weapon3}}.[[weapon_id]]',
                'total_waves' => vsprintf('SUM(CASE %s END) + SUM(CASE %s END)', [
                    implode(' ', [
                        'WHEN {{%salmon_player_weapon3}}.[[wave]] BETWEEN 1 AND 3 THEN 1',
                        'ELSE 0',
                    ]),
                    implode(' ', [
                        'WHEN {{%salmon_player_weapon3}}.[[wave]] <= 3 THEN 0',
                        'WHEN {{%salmon3}}.[[king_salmonid_id]] IS NULL THEN 0',
                        'WHEN {{%salmon3}}.[[clear_extra]] IS NULL THEN 0',
                        'ELSE 1',
                    ]),
                ]),
                'normal_waves' => vsprintf('SUM(CASE %s END)', [
                    implode(' ', [
                        'WHEN {{%salmon_player_weapon3}}.[[wave]] BETWEEN 1 AND 3 THEN 1',
                        'ELSE 0',
                    ]),
                ]),
                'normal_waves_cleared' => vsprintf('SUM(CASE %s END)', [
                    implode(' ', [
                        vsprintf('WHEN (%s) AND (%s) THEN 1', [
                            '{{%salmon_player_weapon3}}.[[wave]] BETWEEN 1 AND 3',
                            '{{%salmon3}}.[[clear_waves]] >= 3',
                        ]),
                        'ELSE 0',
                    ]),
                ]),
                'xtra_waves' => vsprintf('SUM(CASE %s END)', [
                    implode(' ', [
                        'WHEN {{%salmon_player_weapon3}}.[[wave]] <= 3 THEN 0',
                        'WHEN {{%salmon3}}.[[king_salmonid_id]] IS NULL THEN 0',
                        'WHEN {{%salmon3}}.[[clear_extra]] IS NULL THEN 0',
                        'ELSE 1',
                    ]),
                ]),
                'xtra_waves_cleared' => vsprintf('SUM(CASE %s END)', [
                    implode(' ', [
                        'WHEN {{%salmon_player_weapon3}}.[[wave]] <= 3 THEN 0',
                        'WHEN {{%salmon3}}.[[king_salmonid_id]] IS NULL THEN 0',
                        'WHEN {{%salmon3}}.[[clear_extra]] <> TRUE THEN 0',
                        'ELSE 1',
                    ]),
                ]),
            ])
            ->from('{{%salmon3}}')
            ->innerJoin(
                '{{%salmon_player3}}',
                implode(' AND ', [
                    '{{%salmon3}}.[[id]] = {{%salmon_player3}}.[[salmon_id]]',
                    '{{%salmon_player3}}.[[is_me]] = TRUE',
                ]),
            )
            ->innerJoin(
                '{{%salmon_player_weapon3}}',
                '{{%salmon_player3}}.[[id]] = {{%salmon_player_weapon3}}.[[player_id]]',
            )
            ->andWhere(['and',
                [
                    '{{%salmon3}}.[[is_deleted]]' => false,
                    '{{%salmon3}}.[[is_eggstra_work]]' => false,
                    '{{%salmon3}}.[[is_private]]' => false,
                ],
                ['between', '{{%salmon_player_weapon3}}.[[wave]]', 1, 3 + 1],
                ['not', ['{{%salmon_player_weapon3}}.[[weapon_id]]' => null]],
            ])
            ->groupBy([
                '{{%salmon3}}.[[user_id]]',
                '{{%salmon_player_weapon3}}.[[weapon_id]]',
            ]);

        $this->execute(
            vsprintf('INSERT INTO %s (%s) %s', [
                '{{%salmon3_user_stats_weapon}}',
                implode(
                    ', ',
                    array_map(
                        $db->quoteColumnName(...),
                        array_keys($select->select),
                    ),
                ),
                $select->createCommand($db)->rawSql,
            ]),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTables([
            '{{%salmon3_user_stats_weapon}}',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%salmon3_user_stats_weapon}}',
        ];
    }
}

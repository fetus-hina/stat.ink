<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

final class m220829_090529_battle3 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        foreach ($this->tableData() as $tableName => $data) {
            $this->createTable($tableName, $data['schema']);

            $insertData = $data['insert'] ?? null;
            if ($insertData) {
                $this->batchInsert(
                    $tableName,
                    $insertData[0],
                    array_map(
                        function (array $row): array {
                            return array_map(
                                function ($value) {
                                    return $value instanceof Closure ? $value() : $value;
                                },
                                $row
                            );
                        },
                        $insertData[1]
                    )
                );
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTables(
            array_reverse(
                array_keys($this->tableData())
            )
        );

        return true;
    }

    private function tableData(): array
    {
        return [
            '{{%result3}}' => [
                'schema' => [
                    'id' => $this->integer()->notNull(),
                    'key' => $this->apiKey3()->notNull(),
                    'name' => $this->string(32)->notNull(),
                    'is_win' => $this->boolean()->notNull(),
                    'aggregatable' => $this->boolean()->notNull(),
                    'label_color' => $this->string(32)->notNull(),
                    'PRIMARY KEY ([[id]])',
                ],
                'insert' => [
                    ['id', 'key', 'name', 'is_win', 'aggregatable', 'label_color'],
                    [
                        [1, 'win', 'Victory', true, true, 'success'],
                        [2, 'lose', 'Defeat', false, true, 'danger'],
                        [3, 'draw', 'Draw', false, false, 'default'],
                    ],
                ],
            ],
            '{{%lobby3}}' => [
                'schema' => [
                    'id' => $this->primaryKey(),
                    'key' => $this->apiKey3()->notNull(),
                    'name' => $this->string(32)->notNull(),
                    'rank' => $this->integer()->notNull(),
                ],
                'insert' => [
                    ['key', 'name', 'rank'],
                    [
                        ['regular', 'Regular Battle', 110],
                        ['bankara_challenge', 'Anarchy Battle (Series)', 210],
                        ['bankara_open', 'Anarchy Battle (Open)', 220],
                        ['private', 'Private Battle', 900],
                    ],
                ],
            ],
            '{{%rule3}}' => [
                'schema' => [
                    'id' => $this->primaryKey(),
                    'key' => $this->apiKey3()->notNull(),
                    'name' => $this->string(32)->notNull(),
                    'short_name' => $this->string(32)->notNull(),
                    'rank' => $this->integer()->notNull(),
                ],
                'insert' => [
                    ['key', 'name', 'short_name', 'rank'],
                    [
                        ['nawabari', 'Turf War', 'TW', 110],
                        ['area', 'Splat Zones', 'SZ', 210],
                        ['yagura', 'Tower Control', 'TC', 220],
                        ['hoko', 'Rainmaker', 'RM', 230],
                        ['asari', 'Clam Blitz', 'CB', 240],
                    ],
                ],
            ],
            '{{%rank_group3}}' => [
                'schema' => [
                    'id' => $this->primaryKey(),
                    'key' => $this->apiKey3()->notNull(),
                    'name' => $this->string(32)->notNull(),
                    'rank' => $this->integer()->notNull(),
                ],
                'insert' => [
                    ['key', 'name', 'rank'],
                    [
                        ['c', 'C zone', 10],
                        ['b', 'B zone', 20],
                        ['a', 'A zone', 30],
                        ['s', 'S zone', 40],
                    ],
                ],
            ],
            '{{%rank3}}' => [
                'schema' => [
                    'id' => $this->primaryKey(),
                    'key' => $this->apiKey()->notNull(), // Note: not apiKey3() to use "-" and "+"
                    'group_id' => $this->pkRef('{{%rank_group3}}')->notNull(),
                    'name' => $this->string(32)->notNull(),
                    'rank' => $this->integer()->notNull(),
                ],
                'insert' => [
                    ['key', 'name', 'rank', 'group_id'],
                    [
                        ['c-', 'C-', 10, fn () => $this->getRankGroupId('c')],
                        ['c',  'C',  11, fn () => $this->getRankGroupId('c')],
                        ['c+', 'C+', 12, fn () => $this->getRankGroupId('c')],
                        ['b-', 'B-', 20, fn () => $this->getRankGroupId('b')],
                        ['b',  'B',  21, fn () => $this->getRankGroupId('b')],
                        ['b+', 'B+', 22, fn () => $this->getRankGroupId('b')],
                        ['a-', 'A-', 30, fn () => $this->getRankGroupId('a')],
                        ['a',  'A',  31, fn () => $this->getRankGroupId('a')],
                        ['a+', 'A+', 32, fn () => $this->getRankGroupId('a')],
                        ['s',  'S',  41, fn () => $this->getRankGroupId('s')],
                        ['s+', 'S+', 42, fn () => $this->getRankGroupId('s')],
                    ],
                ],
            ],
            '{{%splatoon_version_group3}}' => [
                'schema' => [
                    'id' => $this->primaryKey(),
                    'tag' => $this->string(16)->notNull(),
                    'name' => $this->string(32)->notNull(),
                ],
                'insert' => [
                    ['tag', 'name'],
                    [
                        ['0.0', 'Prerelease'],
                        ['1.0', 'Launch'],
                    ],
                ],
            ],
            '{{%splatoon_version3}}' => [
                'schema' => [
                    'id' => $this->primaryKey(),
                    'tag' => $this->string(16)->notNull(),
                    'group_id' => $this->pkRef('{{%splatoon_version_group3}}')->notNull(),
                    'name' => $this->string(32)->notNull(),
                    'release_at' => 'TIMESTAMP(0) WITH TIME ZONE NOT NULL',
                ],
                'insert' => [
                    ['tag', 'name', 'release_at', 'group_id'],
                    [
                        [
                            '0.0.1',
                            'Prerelease',
                            '2000-01-01T00:00:00+00:00',
                            fn () => $this->getVersionGroupId('0.0'),
                        ],
                        [
                            '1.0.0',
                            'Launch',
                            '2022-09-01T00:00:00+00:00',
                            fn () => $this->getVersionGroupId('1.0'),
                        ],
                    ],
                ],
            ],
            '{{%battle3}}' => [
                'schema' => [
                    'id' => $this->bigPrimaryKey(),
                    'uuid' => 'UUID NOT NULL UNIQUE',
                    'client_uuid' => 'UUID NOT NULL',
                    'user_id' => $this->bigPkRef('{{%user}}')->notNull(),
                    'lobby_id' => $this->pkRef('{{%lobby3}}')->null(),
                    'rule_id' => $this->pkRef('{{%rule3}}')->null(),
                    'map_id' => $this->pkRef('{{%map3}}')->null(),
                    'weapon_id' => $this->pkRef('{{%weapon3}}')->null(),
                    'result_id' => $this->pkRef('{{%result3}}')->null(),
                    'is_knockout' => $this->boolean()->null(),
                    'rank_in_team' => $this->integer()->null(),
                    'kill' => $this->integer()->null(),
                    'assist' => $this->integer()->null(),
                    'kill_or_assist' => $this->integer()->null(),
                    'death' => $this->integer()->null(),
                    'special' => $this->integer()->null(),
                    'inked' => $this->integer()->null(),
                    'our_team_inked' => $this->integer()->null(),
                    'their_team_inked' => $this->integer()->null(),
                    'our_team_percent' => $this->decimal(4, 1)->null(),
                    'their_team_percent' => $this->decimal(4, 1)->null(),
                    'our_team_count' => $this->integer()->null(),
                    'their_team_count' => $this->integer()->null(),
                    'level_before' => $this->integer()->null(),
                    'level_after' => $this->integer()->null(),
                    'rank_before_id' => $this->pkRef('{{%rank3}}')->null(),
                    'rank_before_s_plus' => $this->integer()->null(),
                    'rank_before_exp' => $this->integer()->null(),
                    'rank_after_id' => $this->pkRef('{{%rank3}}')->null(),
                    'rank_after_s_plus' => $this->integer()->null(),
                    'rank_after_exp' => $this->integer()->null(),
                    'cash_before' => $this->integer()->null(),
                    'cash_after' => $this->integer()->null(),
                    'note' => $this->text()->null(),
                    'private_note' => $this->text()->null(),
                    'link_url' => 'httpurl NULL',
                    'version_id' => $this->pkRef('{{%splatoon_version3}}')->null(),
                    'agent_id' => $this->pkRef('{{%agent}}')->null(),
                    'is_automated' => $this->boolean()->notNull()->defaultValue('f'),
                    'use_for_entire' => $this->boolean()->notNull()->defaultValue('f'),
                    'start_at' => 'TIMESTAMP(0) WITH TIME ZONE NULL',
                    'end_at' => 'TIMESTAMP(0) WITH TIME ZONE NULL',
                    'period' => $this->integer()->null(),
                    'remote_addr' => 'INET NOT NULL',
                    'remote_port' => $this->integer()->notNull(),
                    'created_at' => 'TIMESTAMP(0) WITH TIME ZONE NOT NULL',
                    'updated_at' => 'TIMESTAMP(0) WITH TIME ZONE NOT NULL',
                ],
                'insert' => null,
            ],
        ];
    }

    private function getRankGroupId(string $key): int
    {
        return $this->getIntegerId('{{%rank_group3}}', ['key' => $key]);
    }

    private function getVersionGroupId(string $tag): int
    {
        return $this->getIntegerId('{{%splatoon_version_group3}}', ['tag' => $tag]);
    }

    private function getIntegerId(string $table, array $where): int 
    {
        $value = filter_var(
            (new Query())
                ->select('id')
                ->from($table)
                ->andWhere($where)
                ->limit(1)
                ->scalar(),
            FILTER_VALIDATE_INT
        );
        if (is_int($value)) {
            return $value;
        }

        throw new Exception();
    }
}

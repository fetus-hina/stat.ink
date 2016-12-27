<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\commands;

use Yii;
use app\models\BattlePlayer;
use app\models\Knockout;
use app\models\Lobby;
use app\models\Rule;
use app\models\StatAgentUser;
use app\models\StatEntireUser;
use app\models\StatWeapon;
use app\models\StatWeaponBattleCount;
use app\models\StatWeaponKDWinRate;
use app\models\StatWeaponKillDeath;
use app\models\StatWeaponUseCount;
use app\models\StatWeaponUseCountPerWeek;
use app\models\StatWeaponVsWeapon;
use app\components\helpers\Battle as BattleHelper;
use yii\console\Controller;
use yii\helpers\Console;

class StatController extends Controller
{
    /**
     * 全体統計 - ブキ統計を更新します
     *
     * これを実行しないとブキ統計は表示されません。
     */
    public function actionUpdateEntireWeapons()
    {
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        echo "Delete old data...\n";
        StatWeapon::deleteAll();
        StatWeaponBattleCount::deleteAll();
        StatWeaponKillDeath::deleteAll();

        echo "INSERT stat_weapon...\n";
        $select = $this->createSelectQueryForUpdateEntireWeapons();
        $sql = sprintf(
            'INSERT INTO %s (%s) %s',
            $db->quoteTableName(StatWeapon::tableName()),
            implode(', ', array_map(
                function ($k) use ($db) {
                    return $db->quoteColumnName($k);
                },
                array_keys($select->select)
            )),
            $select->createCommand()->rawSql
        );
        $db->createCommand($sql)->execute();

        echo "INSERT stat_weapon_battle_count...\n";
        $select = $this->createSelectQueryForUpdateEntireWeaponsBattleCount();
        $sql = sprintf(
            'INSERT INTO %s (%s) %s',
            $db->quoteTableName(StatWeaponBattleCount::tableName()),
            implode(', ', array_map(
                function ($k) use ($db) {
                    return $db->quoteColumnName($k);
                },
                array_keys($select->select)
            )),
            $select->createCommand()->rawSql
        );
        $db->createCommand($sql)->execute();

        echo "INSERT stat_weapon_kill_death...\n";
        $select = $this->createSelectQueryForUpdateEntireWeaponsKillDeath();
        $sql = sprintf(
            'INSERT INTO %s ( %s) %s',
            $db->quoteTableName(StatWeaponKillDeath::tableName()),
            implode(', ', array_map(
                function ($k) use ($db) {
                    return $db->quoteColumnName($k);
                },
                array_keys($select->select)
            )),
            $select->createCommand()->rawSql
        );
        $db->createCommand($sql)->execute();
        echo "done.\n";
        $transaction->commit();
    }

    private function createSelectQueryForUpdateEntireWeapons()
    {
        $ruleNawabari = Rule::findOne(['key' => 'nawabari'])->id;

        $query = BattlePlayer::find()
            ->innerJoinWith([
                'battle' => function ($q) {
                    return $q->orderBy(null);
                },
                'battle.lobby',
                'weapon',
            ])
            ->andWhere(['{{battle}}.[[is_automated]]' => true])
            ->andWhere(['{{battle}}.[[use_for_entire]]' => true])
            // プライベートバトルを除外
            ->andWhere(['<>', '{{lobby}}.[[key]]', 'private'])
            // 不完全っぽいデータを除外
            ->andWhere(['not', ['{{battle}}.[[is_win]]' => null]])
            ->andWhere(['not', ['{{battle_player}}.[[kill]]' => null]])
            ->andWhere(['not', ['{{battle_player}}.[[death]]' => null]])
            // 自分は集計対象外（重複しまくる）
            ->andWhere(['{{battle_player}}.[[is_me]]' => false])
            ->groupBy('{{battle}}.[[rule_id]], {{battle_player}}.[[weapon_id]]');

        // ルール別の処理を記述
        $query->andWhere(['or',
            // フェスマッチなら味方全部除外（連戦で無意味な重複の可能性が高い）
            // ナワバリは回線落ち判定ができるので回線落ちしたものは除外する
            // 厳密には全く塗らなかった人も除外されるが明らかに外れ値なので気にしない
            ['and',
                ['{{battle}}.[[rule_id]]' => $ruleNawabari],
                ['not', ['{{battle_player}}.[[point]]' => null]],
                ['or',
                    [
                        '{{lobby}}.[[key]]' => 'standard',
                    ],
                    [
                        '{{lobby}}.[[key]]' => 'fest',
                        '{{battle_player}}.[[is_my_team]]' => false,
                    ],
                ],
                ['or',
                    [
                        // 勝ったチームは 300p より大きい
                        'and',
                        // 自分win && 自チーム
                        // 自分lose && 相手チーム
                        // このどちらかなら勝ってるので、結果的に is_win と is_my_team を比較すればいい
                        ['=', '{{battle}}.[[is_win]]', new \yii\db\Expression('battle_player.is_my_team')],
                        ['>', '{{battle_player}}.[[point]]', 300],
                    ],
                    [
                        // 負けたチームは 0p より大きい
                        'and',
                        ['<>', '{{battle}}.[[is_win]]', new \yii\db\Expression('battle_player.is_my_team')],
                        ['>', '{{battle_player}}.[[point]]', 0],
                    ]
                ],
            ],
            // タッグバトルなら味方全部除外（連戦で無意味な重複の可能性が高い）
            ['and',
                ['<>', '{{battle}}.[[rule_id]]', $ruleNawabari],
                ['or',
                    ['not like', '{{lobby}}.[[key]]', 'squad_%', false],
                    ['and',
                        ['like', '{{lobby}}.[[key]]', 'squad_%', false],
                        ['{{battle_player}}.[[is_my_team]]' => false],
                    ],
                ],
            ]
        ]);

        $query->select([
            'rule_id'       => '{{battle}}.[[rule_id]]',
            'weapon_id'     => '{{battle_player}}.[[weapon_id]]',
            'players'       => 'COUNT(*)',
            'total_kill'    => 'SUM({{battle_player}}.[[kill]])',
            'total_death'   => 'SUM({{battle_player}}.[[death]])',
            'win_count' => sprintf(
                'SUM(CASE %s END)',
                implode(' ', [
                    'WHEN {{battle}}.[[is_win]] = TRUE AND {{battle_player}}.[[is_my_team]] = TRUE THEN 1',
                    'WHEN {{battle}}.[[is_win]] = FALSE AND {{battle_player}}.[[is_my_team]] = FALSE THEN 1',
                    'ELSE 0'
                ])
            ),
            'total_point' => sprintf(
                'CASE WHEN {{battle}}.[[rule_id]] <> %d THEN NULL ELSE %s END',
                $ruleNawabari,
                sprintf(
                    'SUM(CASE %s END)',
                    implode(' ', [
                        'WHEN {{battle_player}}.[[point]] IS NULL THEN 0',
                        'WHEN {{battle}}.[[is_win]] = {{battle_player}}.[[is_my_team]] THEN battle_player.point - 300',
                        'ELSE {{battle_player}}.[[point]]',
                    ])
                )
            ),
            'point_available' => sprintf(
                'CASE WHEN {{battle}}.[[rule_id]] <> %d THEN NULL ELSE %s END',
                $ruleNawabari,
                sprintf(
                    'SUM(CASE %s END)',
                    implode(' ', [
                        'WHEN {{battle_player}}.[[point]] IS NULL THEN 0',
                        'ELSE 1',
                    ])
                )
            ),
        ]);

        return $query;
    }

    private function createSelectQueryForUpdateEntireWeaponsBattleCount()
    {
        $query = $this->createSelectQueryForUpdateEntireWeapons();
        $query
            ->select([
                'rule_id' => '{{battle}}.[[rule_id]]',
                'count' => 'COUNT(DISTINCT {{battle_player}}.[[battle_id]])',
            ])
            ->groupBy('{{battle}}.[[rule_id]]');
        return $query;
    }

    private function createSelectQueryForUpdateEntireWeaponsKillDeath()
    {
        $query = (new \yii\db\Query())
            ->select([
                'weapon_id' => '{{p}}.[[weapon_id]]',
                'rule_id'   => '{{b}}.[[rule_id]]',
                'kill'      => '{{p}}.[[kill]]',
                'death'     => '{{p}}.[[death]]',
                'battle'    => 'COUNT(*)',
                'win'       => sprintf(
                    'SUM(CASE %s END)',
                    implode(' ', [
                        'WHEN {{b}}.[[is_win]] = TRUE AND {{p}}.[[is_my_team]] = TRUE THEN 1',
                        'WHEN {{b}}.[[is_win]] = FALSE AND {{p}}.[[is_my_team]] = FALSE THEN 1',
                        'ELSE 0'
                    ])
                ),
            ])
            ->from('{{battle_player}} {{p}}')
            ->innerJoin('{{battle}} {{b}}', '{{p}}.[[battle_id]] = {{b}}.[[id]]')
            ->innerJoin('{{lobby}}', '{{b}}.[[lobby_id]] = {{lobby}}.[[id]]')
            ->innerJoin('{{rule}}', '{{b}}.[[rule_id]] = {{rule}}.[[id]]')
            ->innerJoin('{{weapon}}', '{{p}}.[[weapon_id]] = {{weapon}}.[[id]]')
            ->andWhere(['and',
                ['{{b}}.[[is_automated]]' => true],
                ['{{b}}.[[use_for_entire]]' => true],
                ['<>', '{{lobby}}.[[key]]', 'private'],
                ['not', ['{{p}}.[[kill]]' => null]],
                ['not', ['{{p}}.[[death]]' => null]],
            ])
            ->groupBy([
                '{{p}}.[[weapon_id]]',
                '{{b}}.[[rule_id]]',
                '{{p}}.[[kill]]',
                '{{p}}.[[death]]',
            ]);
        return $query;
    }

    /**
     * 全体統計 - 利用者数を更新します。
     *
     * これを実行しなくてもリアルタイム集計しますが数が増えると死にます。
     */
    public function actionUpdateEntireUser()
    {
        // 集計対象期間を計算する
        $today = (new \DateTime(sprintf('@%d', @$_SERVER['REQUEST_TIME'] ?: time()), null))
            ->setTimeZone(new \DateTimeZone('Etc/GMT-6'))
            ->setTime(0, 0, 0); // 今日の 00:00:00+06 に設定する
        // これで $today より前を抽出すれば前日までのサマリにできる

        $db = Yii::$app->db;
        $db->createCommand("SET timezone TO 'UTC-6'")->execute();
        $transaction = $db->beginTransaction();
        StatEntireUser::deleteAll();
        $db->createCommand()
            ->batchInsert(
                StatEntireUser::tableName(),
                [ 'date', 'battle_count', 'user_count' ],
                array_map(
                    function ($row) {
                        return [
                            $row['date'],
                            $row['battle_count'],
                            $row['user_count']
                        ];
                    },
                    (new \yii\db\Query())
                        ->select([
                            'date'          => '{{battle}}.[[at]]::date',
                            'battle_count'  => 'COUNT({{battle}}.*)',
                            'user_count'    => 'COUNT(DISTINCT {{battle}}.[[user_id]])',
                        ])
                        ->from('battle')
                        ->andWhere(['<', '{{battle}}.[[at]]', $today->format(\DateTime::ATOM)])
                        ->groupBy('{{battle}}.[[at]]::date')
                        ->createCommand()
                        ->queryAll()
                )
            )
            ->execute();
        $transaction->commit();
    }

    /**
     * 全体統計 - エージェント別利用者を更新します。
     */
    public function actionUpdateAgentUser()
    {
        // 集計対象期間を計算する
        $today = (new \DateTime(sprintf('@%d', @$_SERVER['REQUEST_TIME'] ?: time()), null))
            ->setTimeZone(new \DateTimeZone('Etc/GMT-6'))
            ->setTime(0, 0, 0); // 今日の 00:00:00+06 に設定する
        // これで $today より前を抽出すれば前日までのサマリにできる

        $db = Yii::$app->db;
        $db->createCommand("SET timezone TO 'UTC-6'")->execute();
        $transaction = $db->beginTransaction();
        $startDate = (new \DateTime(
            StatAgentUser::find()->max('date') ?? '2015-01-01',
            new \DateTimeZone('Etc/GMT-6')
        ))
            ->setTime(0, 0, 0)
            ->add(new \DateInterval('P1D')); // +1 day

        $insertList = array_map(
            function ($row) {
                return [
                    $row['agent'],
                    $row['date'],
                    $row['battle_count'],
                    $row['user_count']
                ];
            },
            (new \yii\db\Query())
                ->select([
                    'agent'         => '{{agent}}.[[name]]',
                    'date'          => '{{battle}}.[[at]]::date',
                    'battle_count'  => 'COUNT({{battle}}.*)',
                    'user_count'    => 'COUNT(DISTINCT {{battle}}.[[user_id]])',
                ])
                ->from('battle')
                ->innerJoin('agent', '{{battle}}.[[agent_id]] = {{agent}}.[[id]]')
                ->andWhere(['>=', '{{battle}}.[[at]]', $startDate->format(\DateTime::ATOM)])
                ->andWhere(['<', '{{battle}}.[[at]]', $today->format(\DateTime::ATOM)])
                ->andWhere(['<>', '{{agent}}.[[name]]', ''])
                ->groupBy('{{agent}}.[[name]], {{battle}}.[[at]]::date')
                ->createCommand()
                ->queryAll()
        );
        if (!$insertList) {
            return;
        }
        $db->createCommand()
            ->batchInsert(
                StatAgentUser::tableName(),
                [ 'agent', 'date', 'battle_count', 'user_count' ],
                $insertList
            )
            ->execute();
        $transaction->commit();
    }

    /**
     * 全体統計 - ノックアウト率統計を更新します
     *
     * これを実行しないとブキ統計は表示されません。
     */
    public function actionUpdateKnockout()
    {
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        Knockout::deleteAll();
        $db->createCommand()
            ->batchInsert(
                Knockout::tableName(),
                [ 'map_id', 'rule_id', 'battles', 'knockouts' ],
                array_map(
                    function ($row) {
                        return [
                            $row['map_id'],
                            $row['rule_id'],
                            $row['battles'],
                            $row['knockouts'],
                        ];
                    },
                    (new \yii\db\Query())
                        ->select([
                            'map_id'        => '{{battle}}.[[map_id]]',
                            'rule_id'       => '{{battle}}.[[rule_id]]',
                            'battles'       => 'COUNT({{battle}}.*)',
                            'knockouts'     => 'SUM(CASE WHEN {{battle}}.[[is_knock_out]] THEN 1 ELSE 0 END)',
                        ])
                        ->from('battle')
                        ->innerJoin('rule', '{{battle}}.[[rule_id]] = {{rule}}.[[id]]')
                        ->innerJoin('game_mode', '{{rule}}.[[mode_id]] = {{game_mode}}.[[id]]')
                        ->innerJoin('lobby', '{{battle}}.[[lobby_id]] = {{lobby}}.[[id]]')
                        ->innerJoin('map', '{{battle}}.[[map_id]] = {{map}}.[[id]]')
                        ->andWhere(['and',
                            ['not', ['{{battle}}.[[is_win]]' => null]],
                            ['not', ['{{battle}}.[[is_knock_out]]' => null]],
                            ['not', ['{{lobby}}.[[key]]' => 'private']],
                            ['{{game_mode}}.[[key]]' => 'gachi'],
                            ['{{battle}}.[[is_automated]]' => true],
                            ['{{battle}}.[[use_for_entire]]' => true],
                        ])
                        ->groupBy(['{{battle}}.[[map_id]]', '{{battle}}.[[rule_id]]'])
                        ->createCommand()
                        ->queryAll()
                )
            )
            ->execute();
        $transaction->commit();
    }

    /**
     * 全体統計 - KD/勝率データを更新します
     *
     * これを実行しないと当該統計は表示されません。
     */
    public function actionUpdateKDWinRate()
    {
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();

        StatWeaponKDWinRate::deleteAll();

        $select = (new \yii\db\Query())
            ->select([
                'rule_id'       => '{{b}}.[[rule_id]]',
                'map_id'        => '{{b}}.[[map_id]]',
                'weapon_id'     => '{{p}}.[[weapon_id]]',
                'kill'          => '{{p}}.[[kill]]',
                'death'         => '{{p}}.[[death]]',
                'battle_count'  => 'COUNT(*)',
                'win_count'     => 'SUM(CASE WHEN {{b}}.[[is_win]] = {{p}}.[[is_my_team]] THEN 1 ELSE 0 END)',
            ])
            ->from('battle b')
            ->innerJoin('battle_player p', '{{b}}.[[id]] = {{p}}.[[battle_id]]')
            ->innerJoin('lobby', '{{b}}.[[lobby_id]] = {{lobby}}.[[id]]')
            ->innerJoin('rule', '{{b}}.[[rule_id]] = {{rule}}.[[id]]')
            ->where(['and',
                ['not', ['{{b}}.[[map_id]]' => null]],
                ['not', ['{{b}}.[[weapon_id]]' => null]],
                ['not', ['{{b}}.[[is_win]]' => null]],
                ['not', ['{{b}}.[[kill]]' => null]],
                ['not', ['{{b}}.[[death]]' => null]],
                ['not', ['{{p}}.[[weapon_id]]' => null]],
                ['not', ['{{p}}.[[kill]]' => null]],
                ['not', ['{{p}}.[[death]]' => null]],
                ['=', '{{b}}.[[is_automated]]', true],
                ['=', '{{b}}.[[use_for_entire]]', true],
                ['=', '{{p}}.[[is_me]]', false], // 自分を除外
                ['<>', '{{lobby}}.[[key]]', 'private'], // プライベートマッチを除外
            ])
            ->groupBy([
                '{{b}}.[[rule_id]]',
                '{{b}}.[[map_id]]',
                '{{p}}.[[weapon_id]]',
                '{{p}}.[[kill]]',
                '{{p}}.[[death]]',
            ]);
        $select->andWhere(['or',
            // フェスマッチなら味方全部除外（連戦で無意味な重複の可能性が高い）
            // ナワバリは回線落ち判定ができるので回線落ちしたものは除外する
            // 厳密には全く塗らなかった人も除外されるが明らかに外れ値なので気にしない
            ['and',
                ['=', '{{rule}}.[[key]]', 'nawabari'],
                ['not', ['{{p}}.[[point]]' => null]],
                ['or',
                    [
                        '{{lobby}}.[[key]]' => 'standard',
                    ],
                    [
                        '{{lobby}}.[[key]]' => 'fest',
                        '{{p}}.[[is_my_team]]' => false,
                    ],
                ],
                ['or',
                    [
                        // 勝ったチームは 300p より大きい
                        'and',
                        // 自分win && 自チーム
                        // 自分lose && 相手チーム
                        // このどちらかなら勝ってるので、結果的に is_win と is_my_team を比較すればいい
                        ['=', '{{b}}.[[is_win]]', new \yii\db\Expression('p.is_my_team')],
                        ['>', '{{p}}.[[point]]', 300],
                    ],
                    [
                        // 負けたチームは 0p より大きい
                        'and',
                        ['<>', '{{b}}.[[is_win]]', new \yii\db\Expression('p.is_my_team')],
                        ['>', '{{p}}.[[point]]', 0],
                    ]
                ],
            ],
            // タッグバトルなら味方全部除外（連戦で無意味な重複の可能性が高い）
            ['and',
                ['<>', '{{rule}}.[[key]]', 'nawabari'],
                ['or',
                    ['not like', '{{lobby}}.[[key]]', 'squad_%', false],
                    ['and',
                        ['like', '{{lobby}}.[[key]]', 'squad_%', false],
                        ['{{p}}.[[is_my_team]]' => false],
                    ],
                ],
            ]
        ]);

        $sql = sprintf(
            'INSERT INTO {{%s}} ( %s ) %s',
            StatWeaponKDWinRate::tableName(),
            implode(', ', array_map(
                function ($col) {
                    return "[[{$col}]]";
                },
                array_keys($select->select)
            )),
            $select->createCommand()->rawsql
        );
        $db->createCommand($sql)->execute();
        $transaction->commit();
        $db->createCommand(sprintf('VACUUM ANALYZE {{%s}}', StatWeaponKDWinRate::tableName()))->execute();
    }

    /**
     * 全体統計 - ブキ対ブキデータを更新します
     *
     * これを実行しないと当該統計は表示されません。
     */
    public function actionUpdateWeaponVsWeapon()
    {
        $db = Yii::$app->db;
        $constraintName = (function () use ($db) : string {
            $select = (new \yii\db\Query())
                ->select(['constraint_name'])
                ->from('{{information_schema}}.{{table_constraints}}')
                ->andWhere([
                    'table_name' => StatWeaponVsWeapon::tableName(),
                    'constraint_type' => 'PRIMARY KEY',
                ]);
            return $select->scalar($db);
        })();

        $select = (new \yii\db\Query())
            ->select([
                'version_id'    => '{{battle}}.[[version_id]]',
                'rule_id'       => '{{battle}}.[[rule_id]]',
                'weapon_id_1'   => '{{player_lhs}}.[[weapon_id]]',
                'weapon_id_2'   => '{{player_rhs}}.[[weapon_id]]',
                'battle_count'  => 'COUNT(*)',
                'win_count'     => sprintf(
                    'SUM(%s)',
                    'CASE WHEN {{battle}}.[[is_win]] = {{player_lhs}}.[[is_my_team]] THEN 1 ELSE 0 END'
                ),
            ])
            ->from('battle')
            ->innerJoin('lobby', '{{battle}}.[[lobby_id]] = {{lobby}}.[[id]]')
            ->innerJoin('rule', '{{battle}}.[[rule_id]] = {{rule}}.[[id]]')
            ->innerJoin('battle_player player_lhs', '{{battle}}.[[id]] = {{player_lhs}}.[[battle_id]]')
            ->innerJoin('battle_player player_rhs', '(' . implode(' AND ', [
                '{{battle}}.[[id]] = {{player_rhs}}.[[battle_id]]',
                '{{player_lhs}}.[[is_my_team]] <> {{player_rhs}}.[[is_my_team]]',
                '{{player_lhs}}.[[weapon_id]] < {{player_rhs}}.[[weapon_id]]',
            ]) . ')')
            ->andWhere(['and',
                [
                    '{{battle}}.[[is_automated]]' => true,
                    '{{battle}}.[[use_for_entire]]' => true,
                ],
                ['not', ['{{battle}}.[[is_win]]' => null]],
                ['not', ['{{battle}}.[[version_id]]' => null]],
                ['not', ['{{battle}}.[[rule_id]]' => null]],
                ['<>', '{{lobby}}.[[key]]', 'private'],
            ])
            ->groupBy([
                '{{battle}}.[[version_id]]',
                '{{battle}}.[[rule_id]]',
                '{{player_lhs}}.[[weapon_id]]',
                '{{player_rhs}}.[[weapon_id]]',
            ]);

        $upsert = sprintf(
            'INSERT INTO {{%s}} ( %s ) %s ON CONFLICT ON CONSTRAINT [[%s]] DO UPDATE SET %s',
            StatWeaponVsWeapon::tableName(),
            implode(
                ', ',
                array_map(
                    function (string $a) : string {
                        return sprintf('[[%s]]', $a);
                    },
                    array_keys($select->select)
                )
            ),
            $select->createCommand()->rawSql,
            $constraintName,
            implode(', ', [
                '[[battle_count]] = {{excluded}}.[[battle_count]]',
                '[[win_count]] = {{excluded}}.[[win_count]]',
            ])
        );

        $transaction = $db->beginTransaction();
        $db->createCommand($upsert)->execute();
        $transaction->commit();
    }

    /**
     * 全体統計 - ブキ使用数時系列データ
     *
     * これを実行しないと当該統計は表示されません。
     */
    public function actionUpdateWeaponUseCount()
    {
        $db = Yii::$app->db;
        $maxCreatedPeriod = (int)StatWeaponUseCount::find()->max('period');
        $select = (new \yii\db\Query())
            ->select([
                'period'    => '{{battle}}.[[period]]',
                'rule_id'   => '{{battle}}.[[rule_id]]',
                'weapon_id' => '{{battle_player}}.[[weapon_id]]',
                'battles'   => 'COUNT(*)',
                'wins'      => sprintf('SUM(CASE %s END)', implode(' ', [
                    'WHEN {{battle}}.[[is_win]] = {{battle_player}}.[[is_my_team]] THEN 1',
                    'ELSE 0',
                ])),
            ])
            ->from('battle')
            ->innerJoin('lobby', '{{battle}}.[[lobby_id]] = {{lobby}}.[[id]]')
            ->innerJoin('rule', '{{battle}}.[[rule_id]] = {{rule}}.[[id]]')
            ->innerJoin('battle_player', '{{battle}}.[[id]] = {{battle_player}}.[[battle_id]]')
            ->andWhere(['and',
                ['not', ['{{battle}}.[[is_win]]' => null]],
                ['not', ['{{battle}}.[[map_id]]' => null]],
                ['{{battle}}.[[is_automated]]' => true],
                ['{{battle}}.[[use_for_entire]]' => true],
                ['<>', '{{lobby}}.[[key]]', 'private'],
                ['not', ['{{battle_player}}.[[weapon_id]]' => null]],
                ['{{battle_player}}.[[is_me]]' => false],
                ['>', '{{battle}}.[[period]]', (int)StatWeaponUseCount::find()->max('period')],
                ['<', '{{battle}}.[[period]]', \app\components\helpers\Battle::calcPeriod(time())],

                // ルール別の除外設定
                ['or',
                    // ナワバリバトルなら全部 OK
                    ['{{rule}}.[[key]]' => 'nawabari'],

                    // 通常マッチ（とついでにフェス）なら全部 OK
                    ['{{lobby}}.[[key]]' => ['standard', 'fest']],

                    // タッグマッチは敵だけ使う
                    ['and',
                        ['{{battle}}.[[lobby_id]]' => Lobby::find()
                                                            ->select('id')
                                                            ->where(['like', 'key', 'squad_%', false])
                                                            ->column()],
                        ['{{battle_player}}.[[is_my_team]]' => false],
                    ],
                ],
            ])
            ->groupBy(implode(', ', [
                '{{battle}}.[[period]]',
                '{{battle}}.[[rule_id]]',
                '{{battle_player}}.[[weapon_id]]',
            ]));

        $insert = sprintf(
            'INSERT INTO {{%s}} ( %s ) %s',
            StatWeaponUseCount::tablename(),
            implode(', ', array_map(function (string $a) : string {
                return "[[{$a}]]";
            }, array_keys($select->select))),
            $select->createCommand()->rawSql
        );

        $isoYear = "TO_CHAR(PERIOD_TO_TIMESTAMP({{t}}.[[period]]), 'IYYY')::integer";
        $isoWeek = "TO_CHAR(PERIOD_TO_TIMESTAMP({{t}}.[[period]]), 'IW')::integer";
        $maxWeek = StatWeaponUseCountPerWeek::find()
            ->orderBy('[[isoyear]] DESC, [[isoweek]] DESC')
            ->limit(1)
            ->asArray()
            ->one();
        if (!$maxWeek) {
            $maxWeek = [
                'isoyear' => 2015,
                'isoweek' => 1,
            ];
        }
        $selectWeek = (new \yii\db\Query())
            ->select([
                'isoyear'   => $isoYear,
                'isoweek'   => $isoWeek,
                'rule_id'   => '{{t}}.[[rule_id]]',
                'weapon_id' => '{{t}}.[[weapon_id]]',
                'battles'   => 'SUM({{t}}.[[battles]])',
                'wins'      => 'SUM({{t}}.[[wins]])',
            ])
            ->from('stat_weapon_use_count t')
            ->groupBy([
                $isoYear,
                $isoWeek,
                '{{t}}.[[rule_id]]',
                '{{t}}.[[weapon_id]]',
            ])
            ->having(['or',
                ['>', $isoYear, $maxWeek['isoyear']],
                ['and',
                    ['=', $isoYear, $maxWeek['isoyear']],
                    ['>=', $isoWeek, $maxWeek['isoweek']],
                ],
            ]);
        $constraintName = (function () use ($db) : string {
            $select = (new \yii\db\Query())
                ->select(['constraint_name'])
                ->from('{{information_schema}}.{{table_constraints}}')
                ->andWhere([
                    'table_name' => StatWeaponUseCountPerWeek::tableName(),
                    'constraint_type' => 'PRIMARY KEY',
                ]);
            return $select->scalar($db);
        })();
        $upsertWeek = sprintf(
            'INSERT INTO {{%s}} ( %s ) %s ON CONFLICT ON CONSTRAINT [[%s]] DO UPDATE SET %s',
            StatWeaponUseCountPerWeek::tableName(),
            implode(', ', array_map(function (string $a) : string {
                return "[[{$a}]]";
            }, array_keys($selectWeek->select))),
            $selectWeek->createCommand()->rawSql,
            $constraintName,
            implode(', ', [
                '[[battles]] = {{excluded}}.[[battles]]',
                '[[wins]] = {{excluded}}.[[wins]]',
            ])
        );

        $transaction = $db->beginTransaction();
        $db->createCommand("SET timezone TO 'Asia/Tokyo'")->execute();

        echo "Executing {$insert} ...\n";
        $db->createCommand($insert)->execute();

        echo "Executing {$upsertWeek} ...\n";
        $db->createCommand($upsertWeek)->execute();

        $transaction->commit();

        $this->actionUpdateWeaponUseTrend();
    }

    /**
     * 全体統計 - ブキトレンドデータ
     *
     * これを実行しないと当該統計は表示されません。
     */
    public function actionUpdateWeaponUseTrend()
    {
        $db = Yii::$app->db;
        $select = (new \yii\db\Query())
            ->select([
                'rule_id'   => '{{battle}}.[[rule_id]]',
                'map_id'    => '{{battle}}.[[map_id]]',
                'weapon_id' => '{{battle_player}}.[[weapon_id]]',
                'battles'   => 'COUNT(*)',
            ])
            ->from('battle')
            ->innerJoin('lobby', '{{battle}}.[[lobby_id]] = {{lobby}}.[[id]]')
            ->innerJoin('rule', '{{battle}}.[[rule_id]] = {{rule}}.[[id]]')
            ->innerJoin('battle_player', '{{battle}}.[[id]] = {{battle_player}}.[[battle_id]]')
            ->andWhere(['and',
                ['not', ['{{battle}}.[[is_win]]' => null]],
                ['not', ['{{battle}}.[[map_id]]' => null]],
                ['{{battle}}.[[is_automated]]' => true],
                ['{{battle}}.[[use_for_entire]]' => true],
                ['<>', '{{lobby}}.[[key]]', 'private'],
                ['not', ['{{battle_player}}.[[weapon_id]]' => null]],
                ['{{battle_player}}.[[is_me]]' => false],
                ['>=', '{{battle}}.[[period]]', BattleHelper::calcPeriod(time()) - 6 * 30], // 最近 30 日分(= 180 ピリオド)

                // ルール別の除外設定
                ['or',
                    // ナワバリバトルなら全部 OK
                    ['{{rule}}.[[key]]' => 'nawabari'],

                    // 通常マッチ（とついでにフェス）なら全部 OK
                    ['{{lobby}}.[[key]]' => ['standard', 'fest']],

                    // タッグマッチは敵だけ使う
                    ['and',
                        ['{{battle}}.[[lobby_id]]' => Lobby::find()
                                                            ->select('id')
                                                            ->where(['like', 'key', 'squad_%', false])
                                                            ->column()],
                        ['{{battle_player}}.[[is_my_team]]' => false],
                    ],
                ],
            ])
            ->groupBy(implode(', ', [
                '{{battle}}.[[rule_id]]',
                '{{battle}}.[[map_id]]',
                '{{battle_player}}.[[weapon_id]]',
            ]));
        $insertTrend = sprintf(
            'INSERT INTO {{stat_weapon_map_trend}} ( %s ) %s',
            implode(', ', array_map(function (string $a) : string {
                return "[[{$a}]]";
            }, array_keys($select->select))),
            $select->createCommand()->rawSql
        );

        $transaction = $db->beginTransaction();

        echo "Cleanup trend...\n";
        $db->createCommand('DELETE FROM {{stat_weapon_map_trend}}')->execute();

        echo "Insert trend...\n";
        $db->createCommand($insertTrend)->execute();

        $transaction->commit();
    }
}

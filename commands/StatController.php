<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\commands;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use PDO;
use Yii;
use app\commands\stat\Knockout3Trait;
use app\commands\stat\Salmon2Trait;
use app\commands\stat\Salmon3Trait;
use app\commands\stat\Weapon2Trait;
use app\commands\stat\Weapon3Trait;
use app\commands\stat\actions\InkColor3Action;
use app\commands\stat\actions\KDWinRate3Action;
use app\commands\stat\actions\XPowerDistrib3Action;
use app\components\helpers\Battle as BattleHelper;
use app\components\helpers\db\Now;
use app\models\Battle2;
use app\models\BattlePlayer;
use app\models\Knockout;
use app\models\Lobby;
use app\models\Lobby2;
use app\models\Mode2;
use app\models\Rank2;
use app\models\Rule;
use app\models\Rule2;
use app\models\SplatoonVersion2;
use app\models\StatAgentUser;
use app\models\StatAgentUser2;
use app\models\StatEntireUser;
use app\models\StatEntireUser3;
use app\models\StatWeapon;
use app\models\StatWeapon2UseCount;
use app\models\StatWeapon2UseCountPerWeek;
use app\models\StatWeaponBattleCount;
use app\models\StatWeaponKDWinRate;
use app\models\StatWeaponKillDeath;
use app\models\StatWeaponUseCount;
use app\models\StatWeaponUseCountPerWeek;
use app\models\StatWeaponVsWeapon;
use yii\console\Controller;
use yii\db\Connection;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;

use function array_filter;
use function array_keys;
use function array_map;
use function array_merge;
use function array_reverse;
use function array_values;
use function floor;
use function fwrite;
use function implode;
use function sprintf;
use function time;
use function version_compare;
use function vsprintf;

use const SORT_ASC;
use const SORT_DESC;
use const STDERR;

final class StatController extends Controller
{
    use Knockout3Trait;
    use Salmon2Trait;
    use Salmon3Trait;
    use Weapon2Trait;
    use Weapon3Trait;

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'update-ink-color3' => InkColor3Action::class,
            'update-k-d-win-rate3' => KDWinRate3Action::class,
            'update-xpower-distrib3' => XPowerDistrib3Action::class,
        ];
    }

    /**
     * 全体統計 - ブキ統計を更新します
     *
     * これを実行しないとブキ統計は表示されません。
     */
    public function actionUpdateEntireWeapons()
    {
        $this->updateEntireWeapons3();
        // $this->updateEntireWeapons2();
        // $this->updateEntireWeapons1();
    }

    private function updateEntireWeapons1()
    {
        // {{{
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
                fn ($k) => $db->quoteColumnName($k),
                array_keys($select->select),
            )),
            $select->createCommand()->rawSql,
        );
        $db->createCommand($sql)->execute();

        echo "INSERT stat_weapon_battle_count...\n";
        $select = $this->createSelectQueryForUpdateEntireWeaponsBattleCount();
        $sql = sprintf(
            'INSERT INTO %s (%s) %s',
            $db->quoteTableName(StatWeaponBattleCount::tableName()),
            implode(', ', array_map(
                fn ($k) => $db->quoteColumnName($k),
                array_keys($select->select),
            )),
            $select->createCommand()->rawSql,
        );
        $db->createCommand($sql)->execute();

        echo "INSERT stat_weapon_kill_death...\n";
        $select = $this->createSelectQueryForUpdateEntireWeaponsKillDeath();
        $sql = sprintf(
            'INSERT INTO %s ( %s) %s',
            $db->quoteTableName(StatWeaponKillDeath::tableName()),
            implode(', ', array_map(
                fn ($k) => $db->quoteColumnName($k),
                array_keys($select->select),
            )),
            $select->createCommand()->rawSql,
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
                'battle' => fn ($q) => $q->orderBy(null),
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
                        ['=', '{{battle}}.[[is_win]]', new Expression('battle_player.is_my_team')],
                        ['>', '{{battle_player}}.[[point]]', 300],
                    ],
                    [
                        // 負けたチームは 0p より大きい
                        'and',
                        ['<>', '{{battle}}.[[is_win]]', new Expression('battle_player.is_my_team')],
                        ['>', '{{battle_player}}.[[point]]', 0],
                    ],
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
            ],
        ]);

        $query->select([
            'rule_id' => '{{battle}}.[[rule_id]]',
            'weapon_id' => '{{battle_player}}.[[weapon_id]]',
            'players' => 'COUNT(*)',
            'total_kill' => 'SUM({{battle_player}}.[[kill]])',
            'total_death' => 'SUM({{battle_player}}.[[death]])',
            'win_count' => sprintf(
                'SUM(CASE %s END)',
                implode(' ', [
                    'WHEN {{battle}}.[[is_win]] = TRUE AND {{battle_player}}.[[is_my_team]] = TRUE THEN 1',
                    'WHEN {{battle}}.[[is_win]] = FALSE AND {{battle_player}}.[[is_my_team]] = FALSE THEN 1',
                    'ELSE 0',
                ]),
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
                    ]),
                ),
            ),
            'point_available' => sprintf(
                'CASE WHEN {{battle}}.[[rule_id]] <> %d THEN NULL ELSE %s END',
                $ruleNawabari,
                sprintf(
                    'SUM(CASE %s END)',
                    implode(' ', [
                        'WHEN {{battle_player}}.[[point]] IS NULL THEN 0',
                        'ELSE 1',
                    ]),
                ),
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
        return (new Query())
            ->select([
                'weapon_id' => '{{p}}.[[weapon_id]]',
                'rule_id' => '{{b}}.[[rule_id]]',
                'kill' => '{{p}}.[[kill]]',
                'death' => '{{p}}.[[death]]',
                'battle' => 'COUNT(*)',
                'win' => sprintf(
                    'SUM(CASE %s END)',
                    implode(' ', [
                        'WHEN {{b}}.[[is_win]] = TRUE AND {{p}}.[[is_my_team]] = TRUE THEN 1',
                        'WHEN {{b}}.[[is_win]] = FALSE AND {{p}}.[[is_my_team]] = FALSE THEN 1',
                        'ELSE 0',
                    ]),
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
        // }}}
    }

    /**
     * 全体統計 - 利用者数を更新します。
     *
     * これを実行しなくてもリアルタイム集計しますが数が増えると死にます。
     */
    public function actionUpdateEntireUser()
    {
        $this->updateEntireUser1();
        $this->updateEntireUser2();
        $this->updateEntireUser3();
    }

    private function updateEntireUser1()
    {
        // {{{
        // 集計対象期間を計算する
        $today = (new DateTime(sprintf('@%d', @$_SERVER['REQUEST_TIME'] ?: time()), null))
            ->setTimeZone(new DateTimeZone('Etc/GMT-6'))
            ->setTime(0, 0, 0); // 今日の 00:00:00+06 に設定する

        $db = Yii::$app->db;
        $db->createCommand("SET timezone TO 'UTC-6'")->execute();
        $transaction = $db->beginTransaction();
        StatEntireUser::deleteAll();
        $db->createCommand()
            ->batchInsert(
                StatEntireUser::tableName(),
                ['date', 'battle_count', 'user_count'],
                array_map(
                    fn ($row) => [
                            $row['date'],
                            $row['battle_count'],
                            $row['user_count'],
                        ],
                    (new Query())
                        ->select([
                            'date' => '{{battle}}.[[at]]::date',
                            'battle_count' => 'COUNT({{battle}}.*)',
                            'user_count' => 'COUNT(DISTINCT {{battle}}.[[user_id]])',
                        ])
                        ->from('battle')
                        ->andWhere(['<', '{{battle}}.[[at]]', $today->format(DateTime::ATOM)])
                        ->groupBy('{{battle}}.[[at]]::date')
                        ->createCommand()
                        ->queryAll(),
                ),
            )
            ->execute();
        $transaction->commit();
        // }}}
    }

    private function updateEntireUser2()
    {
        // {{{
        // 集計対象期間を計算する
        $today = (new DateTimeImmutable())
            ->setTimestamp($_SERVER['REQUEST_TIME'] ?? time())
            ->setTimeZone(new DateTimeZone('Etc/GMT-6'))
            ->setTime(0, 0, 0); // 今日の 00:00:00+06 に設定する

        $db = Yii::$app->db;
        $db->createCommand("SET timezone TO 'UTC-6'")->execute();
        $cmd = $db
            ->createCommand(
                'INSERT INTO {{stat_entire_user2}} ([[date]], [[battle_count]], [[user_count]]) ' .
                'SELECT ' . implode(', ', [
                    '{{battle2}}.[[created_at]]::date',
                    'COUNT(*) AS [[battle_count]]',
                    'COUNT(DISTINCT {{battle2}}.[[user_id]]) AS [[user_count]]',
                ]) . ' ' .
                'FROM {{battle2}} ' .
                'WHERE ({{battle2}}.[[created_at]] < :today) ' .
                'GROUP BY {{battle2}}.[[created_at]]::date ' .
                'ON CONFLICT ([[date]]) DO UPDATE SET ' .
                '[[battle_count]] = {{excluded}}.[[battle_count]], ' .
                '[[user_count]] = {{excluded}}.[[user_count]]',
            )
            ->bindValue(':today', $today->format(DateTime::ATOM), PDO::PARAM_STR);
        $cmd->execute();
        // }}}
    }

    private function updateEntireUser3(): void
    {
        fwrite(STDERR, "Updating stat_entire_user3...\n");
        Yii::$app->db->transaction(
            function (Connection $db): void {
                $db->createCommand("SET LOCAL TIMEZONE TO 'Etc/UTC'")->execute();

                $today = (new DateTimeImmutable('now', new DateTimeZone('Etc/UTC')))
                    ->setTimestamp($_SERVER['REQUEST_TIME'])
                    ->setTime(0, 0, 0);

                $select = (new Query())
                    ->select([
                        'date' => '([[created_at]]::DATE)',
                        'battles' => 'COUNT(*)',
                        'users' => 'COUNT(DISTINCT [[user_id]])',
                    ])
                    ->from('{{%battle3}}')
                    ->andWhere(['is_deleted' => false])
                    ->andWhere(['<', 'created_at', $today->format(DateTimeInterface::ATOM)])
                    ->groupBy(['([[created_at]]::DATE)']);

                $sql = vsprintf('INSERT INTO %s ( %s ) %s ON CONFLICT ( %s ) DO UPDATE SET %s', [
                    $db->quoteTableName(StatEntireUser3::tableName()),
                    implode(
                        ', ',
                        array_map(
                            fn (string $c): string => $db->quoteColumnName($c),
                            array_keys($select->select),
                        ),
                    ),
                    $select->createCommand($db)->rawSql,
                    $db->quoteColumnName('date'),
                    implode(
                        ', ',
                        array_filter(
                            array_map(
                                fn (string $c): ?string => $c !== 'date'
                                    ? vsprintf('%1$s = %2$s.%1$s', [
                                        $db->quoteColumnName($c),
                                        $db->quoteTableName('excluded'),
                                    ])
                                    : null,
                                array_keys($select->select),
                            ),
                            fn (?string $v): bool => $v !== null,
                        ),
                    ),
                ]);

                $db->createCommand($sql)->execute();
            },
            Transaction::REPEATABLE_READ,
        );
    }

    /**
     * 全体統計 - エージェント別利用者を更新します。
     */
    public function actionUpdateAgentUser()
    {
        $this->updateAgentUser1();
        $this->updateAgentUser2();
    }

    private function updateAgentUser1()
    {
        // {{{
        // 集計対象期間を計算する
        $today = (new DateTime(sprintf('@%d', @$_SERVER['REQUEST_TIME'] ?: time()), null))
            ->setTimeZone(new DateTimeZone('Etc/GMT-6'))
            ->setTime(0, 0, 0); // 今日の 00:00:00+06 に設定する
        // これで $today より前を抽出すれば前日までのサマリにできる

        $db = Yii::$app->db;
        $db->createCommand("SET timezone TO 'UTC-6'")->execute();
        $transaction = $db->beginTransaction();
        $startDate = (new DateTime(
            StatAgentUser::find()->max('date') ?? '2015-01-01',
            new DateTimeZone('Etc/GMT-6'),
        ))
            ->setTime(0, 0, 0)
            ->add(new DateInterval('P1D')); // +1 day

        $insertList = array_map(
            fn ($row) => [
                    $row['agent'],
                    $row['date'],
                    $row['battle_count'],
                    $row['user_count'],
                ],
            (new Query())
                ->select([
                    'agent' => '{{agent}}.[[name]]',
                    'date' => '{{battle}}.[[at]]::date',
                    'battle_count' => 'COUNT({{battle}}.*)',
                    'user_count' => 'COUNT(DISTINCT {{battle}}.[[user_id]])',
                ])
                ->from('battle')
                ->innerJoin('agent', '{{battle}}.[[agent_id]] = {{agent}}.[[id]]')
                ->andWhere(['>=', '{{battle}}.[[at]]', $startDate->format(DateTime::ATOM)])
                ->andWhere(['<', '{{battle}}.[[at]]', $today->format(DateTime::ATOM)])
                ->andWhere(['<>', '{{agent}}.[[name]]', ''])
                ->groupBy('{{agent}}.[[name]], {{battle}}.[[at]]::date')
                ->createCommand()
                ->queryAll(),
        );
        if (!$insertList) {
            $transaction->rollBack();
            return;
        }
        $db->createCommand()
            ->batchInsert(
                StatAgentUser::tableName(),
                ['agent', 'date', 'battle_count', 'user_count'],
                $insertList,
            )
            ->execute();
        $transaction->commit();
        // }}}
    }

    private function updateAgentUser2()
    {
        // {{{
        // 集計対象期間を計算する
        $today = (new DateTimeImmutable())
            ->setTimeZone(new DateTimeZone('Etc/GMT-6'))
            ->setTimestamp($_SERVER['REQUEST_TIME'] ?? time())
            ->setTime(0, 0, 0); // 今日の 00:00:00+06 に設定する

        $db = Yii::$app->db;
        $db->createCommand("SET timezone TO 'UTC-6'")->execute();
        $transaction = $db->beginTransaction();
        $storedMaxDate = StatAgentUser2::find()->max('date') ?? '2017-01-01';
        $startDate = (new DateTimeImmutable($storedMaxDate, new DateTimeZone('Etc/GMT-6')))
            ->setTime(0, 0, 0)
            ->add(new DateInterval('P1D')); // +1 day
        $select = (new Query())
            ->select([
                'agent' => sprintf('(CASE %s END)', implode(' ', [
                    'WHEN {{agent}}.[[name]] IS NULL THEN :unknown_agent',
                    'ELSE {{agent}}.[[name]]',
                ])),
                'date' => '{{battle2}}.[[created_at]]::date',
                'battle_count' => 'COUNT({{battle2}}.*)',
                'user_count' => 'COUNT(DISTINCT {{battle2}}.[[user_id]])',
            ])
            ->from('{{battle2}}')
            ->leftJoin('{{agent}}', '{{battle2}}.[[agent_id]] = {{agent}}.[[id]]')
            ->andWhere(['>=', '{{battle2}}.[[created_at]]', $startDate->format(DateTime::ATOM)])
            ->andWhere(['<', '{{battle2}}.[[created_at]]', $today->format(DateTime::ATOM)])
            ->groupBy(implode(', ', [
                '{{battle2}}.[[created_at]]::date',
                sprintf('(CASE %s END)', implode(' ', [
                    'WHEN {{agent}}.[[name]] IS NULL THEN :unknown_agent',
                    'ELSE {{agent}}.[[name]]',
                ])),
            ]))
            ->orderBy([
                'date' => SORT_ASC,
                'agent' => SORT_ASC,
            ])
            ->createCommand()
            ->bindValue(':unknown_agent', '(unknown)', PDO::PARAM_STR);

        $cmd = Yii::$app->db
            ->createCommand(
                'INSERT INTO "stat_agent_user2"("agent","date","battle_count","user_count") ' .
                $select->rawSql,
            );

        $cmd->execute();
        $transaction->commit();
        // }}}
    }

    /**
     * 全体統計 - ノックアウト率統計を更新します
     *
     * これを実行しないとブキ統計は表示されません。
     */
    public function actionUpdateKnockout()
    {
        $this->updateKnockout3();
        $this->updateKnockout2();
        $this->updateKnockout1();
    }

    private function updateKnockout1()
    {
        // {{{
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        Knockout::deleteAll();
        $db->createCommand()
            ->batchInsert(
                Knockout::tableName(),
                ['map_id', 'rule_id', 'battles', 'knockouts'],
                array_map(
                    fn ($row) => [
                            $row['map_id'],
                            $row['rule_id'],
                            $row['battles'],
                            $row['knockouts'],
                        ],
                    (new Query())
                        ->select([
                            'map_id' => '{{battle}}.[[map_id]]',
                            'rule_id' => '{{battle}}.[[rule_id]]',
                            'battles' => 'COUNT({{battle}}.*)',
                            'knockouts' => 'SUM(CASE WHEN {{battle}}.[[is_knock_out]] THEN 1 ELSE 0 END)',
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
                        ->queryAll(),
                ),
            )
            ->execute();
        $transaction->commit();
        // }}}
    }

    private function updateKnockout2()
    {
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        $timestamp = fn (string $column): string => sprintf('EXTRACT(EPOCH FROM %s)', $column);

        $select = Battle2::find() // {{{
            ->innerJoinWith([
                'lobby',
                'mode',
                'rule',
                'map',
                'battlePlayers' => function ($query) {
                    $query->orderBy(null);
                },
                'battlePlayers.rank',
            ])
            ->where(['and',
                ['not', ['battle2.is_win' => null]],
                ['not', ['battle2.is_knockout' => null]],
                ['not', ['battle2.start_at' => null]],
                ['not', ['battle2.end_at' => null]],
                ['battle2.is_automated' => true],
                ['battle2.use_for_entire' => true],
                ['<>', 'lobby2.key', 'private'],
                ['<>', 'rule2.key', 'nawabari'],
                ['mode2.key' => 'gachi'],
            ])
            ->groupBy([
                'battle2.rule_id',
                'battle2.map_id',
                'battle2.lobby_id',
                'battle_player2.rank_id',
            ])
            ->orderBy(null)
            ->select([
                'rule_id' => 'battle2.rule_id',
                'map_id' => 'battle2.map_id',
                'lobby_id' => 'battle2.lobby_id',
                'rank_id' => 'battle_player2.rank_id',
                'battles' => 'COUNT(*)',
                'knockouts' => sprintf('SUM(CASE %s END)', implode(' ', [
                    'WHEN battle2.is_knockout THEN 1',
                    'ELSE 0',
                ])),
                'avg_game_time' => sprintf(
                    'AVG(%s)',
                    sprintf(
                        '(%s - %s)::double precision',
                        $timestamp('battle2.end_at'),
                        $timestamp('battle2.start_at'),
                    ),
                ),
                'avg_knockout_time' => sprintf('COALESCE(AVG(CASE %s END), 300)', implode(' ', [
                    sprintf(
                        'WHEN battle2.is_knockout THEN (%s - %s)::double precision',
                        $timestamp('battle2.end_at'),
                        $timestamp('battle2.start_at'),
                    ),
                    'ELSE NULL',
                ])),
            ]);
        // }}}

        $insert = 'INSERT INTO knockout2 (' . implode(', ', array_keys($select->select)) . ') ' .
            $select->createCommand()->rawSql . ' ' .
            'ON CONFLICT ( rule_id, map_id, lobby_id, rank_id ) DO UPDATE SET ' .
            implode(', ', array_map(
                fn (string $column): string => sprintf('%1$s = EXCLUDED.%1$s', $column),
                ['battles', 'knockouts', 'avg_game_time', 'avg_knockout_time'],
            ));

        $db->createCommand($insert)->execute();

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

        $select = (new Query())
            ->select([
                'rule_id' => '{{b}}.[[rule_id]]',
                'map_id' => '{{b}}.[[map_id]]',
                'weapon_id' => '{{p}}.[[weapon_id]]',
                'kill' => '{{p}}.[[kill]]',
                'death' => '{{p}}.[[death]]',
                'battle_count' => 'COUNT(*)',
                'win_count' => 'SUM(CASE WHEN {{b}}.[[is_win]] = {{p}}.[[is_my_team]] THEN 1 ELSE 0 END)',
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
                        ['=', '{{b}}.[[is_win]]', new Expression('p.is_my_team')],
                        ['>', '{{p}}.[[point]]', 300],
                    ],
                    [
                        // 負けたチームは 0p より大きい
                        'and',
                        ['<>', '{{b}}.[[is_win]]', new Expression('p.is_my_team')],
                        ['>', '{{p}}.[[point]]', 0],
                    ],
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
            ],
        ]);

        $sql = sprintf(
            'INSERT INTO {{%s}} ( %s ) %s',
            StatWeaponKDWinRate::tableName(),
            implode(', ', array_map(
                fn ($col) => "[[{$col}]]",
                array_keys($select->select),
            )),
            $select->createCommand()->rawsql,
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
        $constraintName = (function () use ($db): string {
            $select = (new Query())
                ->select(['constraint_name'])
                ->from('{{information_schema}}.{{table_constraints}}')
                ->andWhere([
                    'table_name' => StatWeaponVsWeapon::tableName(),
                    'constraint_type' => 'PRIMARY KEY',
                ]);
            return $select->scalar($db);
        })();

        $select = (new Query())
            ->select([
                'version_id' => '{{battle}}.[[version_id]]',
                'rule_id' => '{{battle}}.[[rule_id]]',
                'weapon_id_1' => '{{player_lhs}}.[[weapon_id]]',
                'weapon_id_2' => '{{player_rhs}}.[[weapon_id]]',
                'battle_count' => 'COUNT(*)',
                'win_count' => sprintf(
                    'SUM(%s)',
                    'CASE WHEN {{battle}}.[[is_win]] = {{player_lhs}}.[[is_my_team]] THEN 1 ELSE 0 END',
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
                    fn (string $a): string => sprintf('[[%s]]', $a),
                    array_keys($select->select),
                ),
            ),
            $select->createCommand()->rawSql,
            $constraintName,
            implode(', ', [
                '[[battle_count]] = {{excluded}}.[[battle_count]]',
                '[[win_count]] = {{excluded}}.[[win_count]]',
            ]),
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
        $this->updateWeaponUseCount1();
        $this->updateWeaponUseCount2();
        $this->actionUpdateWeaponUseTrend();
    }

    private function updateWeaponUseCount1()
    {
        // {{{
        $db = Yii::$app->db;
        $maxCreatedPeriod = (int)StatWeaponUseCount::find()->max('period');
        $select = (new Query())
            ->select([
                'period' => '{{battle}}.[[period]]',
                'rule_id' => '{{battle}}.[[rule_id]]',
                'weapon_id' => '{{battle_player}}.[[weapon_id]]',
                'battles' => 'COUNT(*)',
                'wins' => sprintf('SUM(CASE %s END)', implode(' ', [
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
                ['<', '{{battle}}.[[period]]', BattleHelper::calcPeriod(time())],

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
            implode(', ', array_map(fn (string $a): string => "[[{$a}]]", array_keys($select->select))),
            $select->createCommand()->rawSql,
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
        $selectWeek = (new Query())
            ->select([
                'isoyear' => $isoYear,
                'isoweek' => $isoWeek,
                'rule_id' => '{{t}}.[[rule_id]]',
                'weapon_id' => '{{t}}.[[weapon_id]]',
                'battles' => 'SUM({{t}}.[[battles]])',
                'wins' => 'SUM({{t}}.[[wins]])',
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
        $constraintName = (function () use ($db): string {
            $select = (new Query())
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
            implode(', ', array_map(fn (string $a): string => "[[{$a}]]", array_keys($selectWeek->select))),
            $selectWeek->createCommand()->rawSql,
            $constraintName,
            implode(', ', [
                '[[battles]] = {{excluded}}.[[battles]]',
                '[[wins]] = {{excluded}}.[[wins]]',
            ]),
        );

        $transaction = $db->beginTransaction();
        $db->createCommand("SET timezone TO 'Asia/Tokyo'")->execute();

        echo "Executing {$insert} ...\n";
        $db->createCommand($insert)->execute();
        echo "\n";

        echo "Executing {$upsertWeek} ...\n";
        $db->createCommand($upsertWeek)->execute();
        echo "\n";

        echo "Commiting...\n";
        $transaction->commit();
        // }}}
    }

    private function updateWeaponUseCount2()
    {
        $db = Yii::$app->db;
        // {{{
        // $maxCreatedPeriod = (int)StatWeapon2UseCount::find()->max('period');
        $select = Battle2::find()
            ->select([
                'period' => '{{battle2}}.[[period]]',
                'rule_id' => '{{battle2}}.[[rule_id]]',
                'weapon_id' => '{{battle_player2}}.[[weapon_id]]',
                'map_id' => '{{battle2}}.[[map_id]]',
                'battles' => 'COUNT(*)',
                'wins' => sprintf('SUM(CASE %s END)', implode(' ', [
                    'WHEN {{battle2}}.[[is_win]] = {{battle_player2}}.[[is_my_team]] THEN 1',
                    'ELSE 0',
                ])),
                'kills' => sprintf('SUM(CASE %s END)', implode(' ', [
                    'WHEN {{battle_player2}}.[[kill]] IS NULL THEN 0',
                    'WHEN {{battle_player2}}.[[death]] IS NULL THEN 0',
                    'ELSE {{battle_player2}}.[[kill]]',
                ])),
                'deaths' => sprintf('SUM(CASE %s END)', implode(' ', [
                    'WHEN {{battle_player2}}.[[kill]] IS NULL THEN 0',
                    'WHEN {{battle_player2}}.[[death]] IS NULL THEN 0',
                    'ELSE {{battle_player2}}.[[death]]',
                ])),
                'kd_available' => sprintf('SUM(CASE %s END)', implode(' ', [
                    'WHEN {{battle_player2}}.[[kill]] IS NULL THEN 0',
                    'WHEN {{battle_player2}}.[[death]] IS NULL THEN 0',
                    'ELSE 1',
                ])),
                'kills_with_time' => sprintf('SUM(CASE %s END)', implode(' ', [
                    'WHEN {{battle_player2}}.[[kill]] IS NULL THEN 0',
                    'WHEN {{battle_player2}}.[[death]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[start_at]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[end_at]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[start_at]] >= {{battle2}}.[[end_at]] IS NULL THEN 0',
                    'ELSE {{battle_player2}}.[[kill]]',
                ])),
                'deaths_with_time' => sprintf('SUM(CASE %s END)', implode(' ', [
                    'WHEN {{battle_player2}}.[[kill]] IS NULL THEN 0',
                    'WHEN {{battle_player2}}.[[death]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[start_at]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[end_at]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[start_at]] >= {{battle2}}.[[end_at]] IS NULL THEN 0',
                    'ELSE {{battle_player2}}.[[death]]',
                ])),
                'kd_time_available' => sprintf('SUM(CASE %s END)', implode(' ', [
                    'WHEN {{battle_player2}}.[[kill]] IS NULL THEN 0',
                    'WHEN {{battle_player2}}.[[death]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[start_at]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[end_at]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[start_at]] >= {{battle2}}.[[end_at]] IS NULL THEN 0',
                    'ELSE 1',
                ])),
                'kd_time_seconds' => sprintf('SUM(CASE %s END)', implode(' ', [
                    'WHEN {{battle_player2}}.[[kill]] IS NULL THEN 0',
                    'WHEN {{battle_player2}}.[[death]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[start_at]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[end_at]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[start_at]] >= {{battle2}}.[[end_at]] IS NULL THEN 0',
                    sprintf(
                        'ELSE (EXTRACT(EPOCH FROM %s) - EXTRACT(EPOCH FROM %s))',
                        '{{battle2}}.[[end_at]]',
                        '{{battle2}}.[[start_at]]',
                    ),
                ])),
                'specials' => sprintf('SUM(CASE %s END)', implode(' ', [
                    'WHEN {{battle_player2}}.[[special]] IS NULL THEN 0',
                    'ELSE {{battle_player2}}.[[special]]',
                ])),
                'specials_available' => sprintf('SUM(CASE %s END)', implode(' ', [
                    'WHEN {{battle_player2}}.[[special]] IS NULL THEN 0',
                    'ELSE 1',
                ])),
                'specials_with_time' => sprintf('SUM(CASE %s END)', implode(' ', [
                    'WHEN {{battle_player2}}.[[special]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[start_at]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[end_at]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[start_at]] >= {{battle2}}.[[end_at]] IS NULL THEN 0',
                    'ELSE {{battle_player2}}.[[special]]',
                ])),
                'specials_time_available' => sprintf('SUM(CASE %s END)', implode(' ', [
                    'WHEN {{battle_player2}}.[[special]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[start_at]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[end_at]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[start_at]] >= {{battle2}}.[[end_at]] IS NULL THEN 0',
                    'ELSE 1',
                ])),
                'specials_time_seconds' => sprintf('SUM(CASE %s END)', implode(' ', [
                    'WHEN {{battle_player2}}.[[special]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[start_at]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[end_at]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[start_at]] >= {{battle2}}.[[end_at]] IS NULL THEN 0',
                    sprintf(
                        'ELSE (EXTRACT(EPOCH FROM %s) - EXTRACT(EPOCH FROM %s))',
                        '{{battle2}}.[[end_at]]',
                        '{{battle2}}.[[start_at]]',
                    ),
                ])),
                'inked' => sprintf('SUM(CASE %s END)', implode(' ', [
                    'WHEN {{battle_player2}}.[[point]] IS NULL THEN 0',
                    "WHEN {{rule2}}.[[key]] <> 'nawabari' THEN {{battle_player2}}.[[point]]",
                    sprintf(
                        'WHEN %s THEN %s',
                        '{{battle2}}.[[is_win]] = {{battle_player2}}.[[is_my_team]]',
                        '({{battle_player2}}.[[point]] - 1000)',
                    ),
                    'ELSE {{battle_player2}}.[[point]]',
                ])),
                'inked_available' => sprintf('SUM(CASE %s END)', implode(' ', [
                    'WHEN {{battle_player2}}.[[point]] IS NULL THEN 0',
                    'ELSE 1',
                ])),
                'inked_with_time' => sprintf('SUM(CASE %s END)', implode(' ', [
                    'WHEN {{battle_player2}}.[[point]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[start_at]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[end_at]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[start_at]] >= {{battle2}}.[[end_at]] IS NULL THEN 0',
                    "WHEN {{rule2}}.[[key]] <> 'nawabari' THEN {{battle_player2}}.[[point]]",
                    sprintf(
                        'WHEN %s THEN %s',
                        '{{battle2}}.[[is_win]] = {{battle_player2}}.[[is_my_team]]',
                        '({{battle_player2}}.[[point]] - 1000)',
                    ),
                    'ELSE {{battle_player2}}.[[point]]',
                ])),
                'inked_time_available' => sprintf('SUM(CASE %s END)', implode(' ', [
                    'WHEN {{battle_player2}}.[[point]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[start_at]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[end_at]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[start_at]] >= {{battle2}}.[[end_at]] IS NULL THEN 0',
                    'ELSE 1',
                ])),
                'inked_time_seconds' => sprintf('SUM(CASE %s END)', implode(' ', [
                    'WHEN {{battle_player2}}.[[point]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[start_at]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[end_at]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[start_at]] >= {{battle2}}.[[end_at]] IS NULL THEN 0',
                    sprintf(
                        'ELSE (EXTRACT(EPOCH FROM %s) - EXTRACT(EPOCH FROM %s))',
                        '{{battle2}}.[[end_at]]',
                        '{{battle2}}.[[start_at]]',
                    ),
                ])),
                'knockout_wins' => sprintf('SUM(CASE %s END)', implode(' ', [
                    "WHEN {{rule2}}.[[key]] = 'nawabari' THEN NULL",
                    'WHEN {{battle2}}.[[is_knockout]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[is_knockout]] = FALSE THEN 0',
                    'WHEN {{battle2}}.[[is_win]] <> {{battle_player2}}.[[is_my_team]] THEN 0',
                    'ELSE 1',
                ])),
                'timeup_wins' => sprintf('SUM(CASE %s END)', implode(' ', [
                    "WHEN {{rule2}}.[[key]] = 'nawabari' THEN NULL",
                    'WHEN {{battle2}}.[[is_knockout]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[is_knockout]] = TRUE THEN 0',
                    'WHEN {{battle2}}.[[is_win]] <> {{battle_player2}}.[[is_my_team]] THEN 0',
                    'ELSE 1',
                ])),
                'knockout_loses' => sprintf('SUM(CASE %s END)', implode(' ', [
                    "WHEN {{rule2}}.[[key]] = 'nawabari' THEN NULL",
                    'WHEN {{battle2}}.[[is_knockout]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[is_knockout]] = FALSE THEN 0',
                    'WHEN {{battle2}}.[[is_win]] = {{battle_player2}}.[[is_my_team]] THEN 0',
                    'ELSE 1',
                ])),
                'timeup_loses' => sprintf('SUM(CASE %s END)', implode(' ', [
                    "WHEN {{rule2}}.[[key]] = 'nawabari' THEN NULL",
                    'WHEN {{battle2}}.[[is_knockout]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[is_knockout]] = TRUE THEN 0',
                    'WHEN {{battle2}}.[[is_win]] = {{battle_player2}}.[[is_my_team]] THEN 0',
                    'ELSE 1',
                ])),
            ])
            ->innerJoinWith([
                'lobby',
                'mode',
                'rule',
                'battlePlayers' => function ($query) {
                    $query->orderBy(null);
                },
            ])
            ->andWhere(['and',
                ['not', ['{{battle2}}.[[is_win]]' => null]],
                ['not', ['{{battle2}}.[[map_id]]' => null]],
                ['{{battle2}}.[[is_automated]]' => true],
                ['{{battle2}}.[[use_for_entire]]' => true],
                ['<>', '{{lobby2}}.[[key]]', 'private'],
                ['<>', '{{mode2}}.[[key]]', 'private'],
                ['not', ['{{battle_player2}}.[[weapon_id]]' => null]],
                ['{{battle_player2}}.[[is_me]]' => false],
                // ['>', '{{battle2}}.[[period]]', $maxCreatedPeriod],
                ['<', '{{battle2}}.[[period]]', BattleHelper::calcPeriod2(time())],
            ])
            // ルール別除外処理
            ->andWhere(['or',
                // レギュラーマッチなら自分以外全員使う
                ['{{mode2}}.[[key]]' => 'regular'],

                // フェスマッチでソロなら自分以外全員使う
                // フェスマッチでチームなら敵チームだけ使う
                ['and',
                    ['{{mode2}}.[[key]]' => 'fest'],
                    ['or',
                        ['{{lobby2}}.[[key]]' => 'standard'],
                        ['and',
                            ['{{lobby2}}.[[key]]' => 'squad_4'],
                            ['{{battle_player2}}.[[is_my_team]]' => false],
                        ],
                    ],
                ],

                // ガチマッチ（ソロ）なら自分以外全員使う
                ['and',
                    ['{{mode2}}.[[key]]' => 'gachi'],
                    ['{{lobby2}}.[[key]]' => 'standard'],
                ],

                // タッグマッチは敵だけ使う
                ['and',
                    ['{{mode2}}.[[key]]' => 'gachi'],
                    ['{{lobby2}}.[[key]]' => ['squad_2', 'squad_4']],
                    ['{{battle_player2}}.[[is_my_team]]' => false],
                ],
            ])
            ->groupBy(implode(', ', [
                '{{battle2}}.[[period]]',
                '{{battle2}}.[[rule_id]]',
                '{{battle_player2}}.[[weapon_id]]',
                '{{battle2}}.[[map_id]]',
            ]))
            ->orderBy(null);
        $sql = $select->createCommand()->rawSql;

        $insert = sprintf(
            'INSERT INTO {{%s}} ( %s ) %s',
            StatWeapon2UseCount::tablename(),
            implode(', ', array_map(fn (string $a): string => "[[{$a}]]", array_keys($select->select))),
            $select->createCommand()->rawSql,
        );
        $insert .= sprintf(
            ' ON CONFLICT ( %s ) DO UPDATE SET %s',
            implode(', ', array_map(
                fn ($column) => '[[' . $column . ']]',
                ['period', 'rule_id', 'weapon_id', 'map_id'],
            )),
            implode(', ', array_map(
                fn ($column) => sprintf('[[%1$s]] = {{excluded}}.[[%1$s]]', $column),
                [
                    'battles',
                    'wins',
                    'kills',
                    'deaths',
                    'kd_available',
                    'kills_with_time',
                    'deaths_with_time',
                    'kd_time_available',
                    'kd_time_seconds',
                    'specials',
                    'specials_available',
                    'specials_with_time',
                    'specials_time_available',
                    'specials_time_seconds',
                    'inked',
                    'inked_available',
                    'inked_with_time',
                    'inked_time_available',
                    'inked_time_seconds',
                    'knockout_wins',
                    'timeup_wins',
                    'knockout_loses',
                    'timeup_loses',
                ],
            )),
        );
        // }}}

        // {{{
        $isoYear = "TO_CHAR(PERIOD2_TO_TIMESTAMP({{t}}.[[period]]), 'IYYY')::integer";
        $isoWeek = "TO_CHAR(PERIOD2_TO_TIMESTAMP({{t}}.[[period]]), 'IW')::integer";
        $maxWeek = StatWeapon2UseCountPerWeek::find()
            ->orderBy([
                '[[isoyear]]' => SORT_DESC,
                '[[isoweek]]' => SORT_DESC,
            ])
            ->limit(1)
            ->asArray()
            ->one();
        if (!$maxWeek) {
            $maxWeek = [
                'isoyear' => 2017,
                'isoweek' => 1,
            ];
        }
        $columns = [
            'battles',
            'wins',
            'kills',
            'deaths',
            'kd_available',
            'kills_with_time',
            'deaths_with_time',
            'kd_time_available',
            'kd_time_seconds',
            'specials',
            'specials_available',
            'specials_with_time',
            'specials_time_available',
            'specials_time_seconds',
            'inked',
            'inked_available',
            'inked_with_time',
            'inked_time_available',
            'inked_time_seconds',
            'knockout_wins',
            'timeup_wins',
            'knockout_loses',
            'timeup_loses',
        ];
        $selectWeek = (new Query())
            ->select(array_merge(
                [
                    'isoyear' => $isoYear,
                    'isoweek' => $isoWeek,
                    'rule_id' => '{{t}}.[[rule_id]]',
                    'weapon_id' => '{{t}}.[[weapon_id]]',
                    'map_id' => '{{t}}.[[map_id]]',
                ],
                ArrayHelper::map(
                    $columns,
                    fn (string $colName): string => $colName,
                    fn (string $colName): string => "SUM({{t}}.[[{$colName}]])",
                ),
            ))
            ->from(sprintf('{{%s}} {{t}}', StatWeapon2UseCount::tableName()))
            ->groupBy([
                $isoYear,
                $isoWeek,
                '{{t}}.[[rule_id]]',
                '{{t}}.[[weapon_id]]',
                '{{t}}.[[map_id]]',
            ])
            ->having(['or',
                ['>', $isoYear, $maxWeek['isoyear']],
                ['and',
                    ['=', $isoYear, $maxWeek['isoyear']],
                    ['>=', $isoWeek, $maxWeek['isoweek']],
                ],
            ]);
        $upsertWeek = sprintf(
            'INSERT INTO {{%s}} ( %s ) %s ON CONFLICT ( %s ) DO UPDATE SET %s',
            StatWeapon2UseCountPerWeek::tableName(),
            implode(', ', array_map(fn (string $a): string => "[[{$a}]]", array_keys($selectWeek->select))),
            $selectWeek->createCommand()->rawSql,
            implode(', ', array_map(fn (string $a): string => "[[{$a}]]", ['isoyear', 'isoweek', 'rule_id', 'weapon_id', 'map_id'])),
            implode(', ', array_map(fn (string $a): string => sprintf('[[%1$s]] = {{excluded}}.[[%1$s]]', $a), ['battles', 'wins'])),
        );
        // }}}

        $transaction = $db->beginTransaction();
        $db->createCommand("SET timezone TO 'Asia/Tokyo'")->execute();

        echo "Executing {$insert} ...\n";
        $db->createCommand($insert)->execute();
        echo "\n";

        echo "Executing {$upsertWeek} ...\n";
        $db->createCommand($upsertWeek)->execute();
        echo "\n";

        echo "Commiting...\n";
        $transaction->commit();

        echo "Vacuum...\n";
        $db->createCommand('VACUUM ANALYZE {{stat_weapon2_use_count}}')->execute();
        $db->createCommand('VACUUM ANALYZE {{stat_weapon2_use_count_per_week}}')->execute();
        // }}}
    }

    /**
     * 全体統計 - ブキトレンドデータ
     *
     * これを実行しないと当該統計は表示されません。
     */
    public function actionUpdateWeaponUseTrend()
    {
        $db = Yii::$app->db;
        $select = (new Query())
            ->select([
                'rule_id' => '{{battle}}.[[rule_id]]',
                'map_id' => '{{battle}}.[[map_id]]',
                'weapon_id' => '{{battle_player}}.[[weapon_id]]',
                'battles' => 'COUNT(*)',
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
            implode(', ', array_map(fn (string $a): string => "[[{$a}]]", array_keys($select->select))),
            $select->createCommand()->rawSql,
        );

        $transaction = $db->beginTransaction();

        echo "Cleanup trend...\n";
        $db->createCommand('DELETE FROM {{stat_weapon_map_trend}}')->execute();

        echo "Insert trend...\n";
        $db->createCommand($insertTrend)->execute();

        $transaction->commit();
    }

    /**
     * プレーヤーIDと投稿者紐付けデータ
     *
     * これを実行しないとバトルのプレーヤーリストからユーザへのリンクや
     * アイコン表示が機能しません。
     */
    public function actionUpdatePlayerIdMap()
    {
        $db = Yii::$app->db;

        echo "Updating splatnet2_user_map...\n";
        $sql = 'INSERT INTO {{splatnet2_user_map}} ( [[splatnet_id]], [[user_id]], [[battles]] ) ' .
            'SELECT {{battle_player2}}.[[splatnet_id]], {{battle2}}.[[user_id]], COUNT(*) AS [[battles]] ' .
            'FROM {{battle_player2}} ' .
            'INNER JOIN {{battle2}} ON {{battle_player2}}.[[battle_id]] = {{battle2}}.[[id]] ' .
            'WHERE {{battle_player2}}.[[splatnet_id]] IS NOT NULL ' .
            'AND {{battle_player2}}.[[is_me]] = TRUE ' .
            'GROUP BY {{battle_player2}}.[[splatnet_id]], {{battle2}}.[[user_id]] ' .
            'ON CONFLICT ( [[splatnet_id]], [[user_id]] ) DO UPDATE SET ' .
            '[[battles]] = {{excluded}}.[[battles]] ';
        $db->createCommand($sql)->execute();
        echo "done.\n";
        echo "VACUUM...\n";
        $db->createCommand('VACUUM ANALYZE {{splatnet2_user_map}}')->execute();
        echo "done.\n";
    }

    public function actionUpdateWeaponTier2(): void
    {
        Yii::$app->db->transaction(function ($db): void {
            $lobby = Lobby2::findOne(['key' => 'standard']);
            $mode = Mode2::findOne(['key' => 'gachi']);
            $ruleIds = ArrayHelper::getColumn(
                Rule2::findAll(['key' => ['area', 'yagura', 'hoko', 'asari']]),
                'id',
            );
            $month = $this->createPeriodToMonthColumnForSplatoon2();
            $per5min = function (string $column): string {
                $battleSec = 'EXTRACT(EPOCH FROM {{battle2}}.[[end_at]] - {{battle2}}.[[start_at]])';
                return vsprintf('(%s::float * 300.0 / %s)', [
                    $column,
                    $battleSec,
                ]);
            };
            $query = (new Query()) // {{{
                ->from('battle2')
                ->andWhere(['and',
                    [
                        '{{battle2}}.[[lobby_id]]' => $lobby->id,
                        '{{battle2}}.[[mode_id]]' => $mode->id,
                        '{{battle2}}.[[rule_id]]' => $ruleIds,
                        '{{battle2}}.[[is_win]]' => [true, false],
                        '{{battle2}}.[[is_automated]]' => true,
                        '{{battle2}}.[[use_for_entire]]' => true,
                    ],
                    $this->createHighestRankFilter(),
                    ['BETWEEN',
                        '{{battle2}}.[[end_at]] - {{battle2}}.[[start_at]]',
                        new Expression("'30 seconds'::interval"),
                        new Expression("'600 seconds'::interval"),
                    ],
                ])
                ->innerJoin('battle_player2', '{{battle2}}.[[id]] = {{battle_player2}}.[[battle_id]]')
                ->andWhere(['and',
                    ['{{battle_player2}}.[[is_me]]' => false],
                    ['not', ['{{battle_player2}}.[[kill]]' => null]],
                    ['not', ['{{battle_player2}}.[[death]]' => null]],
                ])
                ->innerJoin(['w' => 'weapon2'], '{{battle_player2}}.[[weapon_id]] = {{w}}.[[id]]')
                ->innerJoin(['c' => 'weapon2'], '{{w}}.[[canonical_id]] = {{c}}.[[id]]')
                ->innerJoin(['v' => 'splatoon_version2'], '{{battle2}}.[[version_id]] = {{v}}.[[id]]')
                ->groupBy([
                    '{{v}}.[[group_id]]',
                    $month,
                    '{{battle2}}.[[rule_id]]',
                    '{{c}}.[[id]]',
                ])
                ->andHaving(['and',
                    ['>=', 'COUNT(*)', 2],
                ])
                ->orderBy([
                    '{{v}}.[[group_id]]' => SORT_ASC,
                    '[[month]]' => SORT_ASC,
                    '{{battle2}}.[[rule_id]]' => SORT_ASC,
                    '[[win_percent]]' => SORT_DESC,
                ])
                ->select([
                    'version_group_id' => '{{v}}.[[group_id]]',
                    'month' => "($month)::date",
                    'rule_id' => '{{battle2}}.[[rule_id]]',
                    'weapon_id' => '{{c}}.[[id]]',
                    'players_count' => 'COUNT(*)',
                    'win_count' => sprintf('SUM(CASE %s END)', implode(' ', [
                        'WHEN {{battle2}}.[[is_win]] = {{battle_player2}}.[[is_my_team]] THEN 1',
                        'ELSE 0',
                    ])),
                    'win_percent' => vsprintf('(100.0 * %s / %s)', [
                        sprintf('SUM(CASE %s END)', implode(' ', [
                            'WHEN {{battle2}}.[[is_win]] = {{battle_player2}}.[[is_my_team]] THEN 1',
                            'ELSE 0',
                        ])),
                        'COUNT(*)',
                    ]),
                    'avg_kill' => sprintf('AVG(%s)', $per5min('{{battle_player2}}.[[kill]]')),
                    'med_kill' => sprintf(
                        'PERCENTILE_CONT(0.5) WITHIN GROUP (ORDER BY %s)',
                        $per5min('{{battle_player2}}.[[kill]]'),
                    ),
                    'stderr_kill' => sprintf(
                        'STDDEV_SAMP(%s) / SQRT(COUNT(*))',
                        $per5min('{{battle_player2}}.[[kill]]'),
                    ),
                    'stddev_kill' => sprintf(
                        'STDDEV_SAMP(%s)',
                        $per5min('{{battle_player2}}.[[kill]]'),
                    ),
                    'avg_death' => sprintf('AVG(%s)', $per5min('{{battle_player2}}.[[death]]')),
                    'med_death' => sprintf(
                        'PERCENTILE_CONT(0.5) WITHIN GROUP (ORDER BY %s)',
                        $per5min('{{battle_player2}}.[[death]]'),
                    ),
                    'stderr_death' => sprintf(
                        'STDDEV_SAMP(%s) / SQRT(COUNT(*))',
                        $per5min('{{battle_player2}}.[[death]]'),
                    ),
                    'stddev_death' => sprintf(
                        'STDDEV_SAMP(%s)',
                        $per5min('{{battle_player2}}.[[death]]'),
                    ),
                    'updated_at' => 'NOW()',
                ]);
            // }}}
            $db->createCommand('DELETE FROM {{stat_weapon2_tier}}')->execute();
            $db->createCommand()->insert('stat_weapon2_tier', $query)->execute();
        });
        Yii::$app->db->createCommand('VACUUM ANALYZE {{stat_weapon2_tier}}')->execute();
    }

    private function createHighestRankFilter(): array
    {
        // {{{
        $result = ['or'];
        foreach ($this->getHighestRankMap() as $rankId => $versionIds) {
            $result[] = ['and', [
                '{{battle2}}.[[rank_id]]' => $rankId,
                '{{battle2}}.[[version_id]]' => $versionIds,
            ]];
        }
        return $result;
        // }}}
    }

    private function getHighestRankMap(): array
    {
        // {{{
        $rankSP = Rank2::findOne(['key' => 's+']);
        $rankX = Rank2::findOne(['key' => 'x']);
        $xVersions = [];
        $spVersions = [];
        foreach ($this->getReleasedVersions() as $version) {
            if (version_compare($version->tag, '3.0.0', '>=')) {
                $xVersions[] = $version->id;
            } else {
                $spVersions[] = $version->id;
            }
        }
        return [
            $rankSP->id => $spVersions,
            $rankX->id => $xVersions,
        ];
        // }}}
    }

    private function createPeriodToMonthColumnForSplatoon2(): string
    {
        $data = $this->getMonthlyPeriodRangeForSplatoon2();
        return sprintf('(CASE %s END)', implode(' ', array_map(
            fn (string $month, array $period): string => vsprintf('WHEN {{battle2}}.[[period]] >= %d THEN %s', [
                    $period[0],
                    Yii::$app->db->quoteValue($month),
                ]),
            array_reverse(array_keys($data)),
            array_reverse(array_values($data)),
        )));
    }

    private function getMonthlyPeriodRangeForSplatoon2(): array
    {
        // {{{
        $now = new DateTimeImmutable('now', new DateTimeZone('Etc/UTC'));
        $yMax = (int)$now->format('Y');
        $mMax = (int)$now->format('n');

        $results = [];
        for ($y = 2017; $y <= $yMax; ++$y) {
            for ($m = 1; $m <= 12; ++$m) {
                if ($y === 2017 && $m < 7) {
                    continue;
                } elseif ($y === $yMax && $m > $mMax) {
                    continue;
                }

                $month = $now->setDate($y, $m, 1)->setTime(0, 0, 0);
                $next = $month->add(new DateInterval('P1M'));
                $results[$month->format('Y-m-d')] = [
                    (int)floor($month->getTimestamp() / 7200),
                    (int)floor($next->getTimestamp() / 7200 - 1),
                ];
            }
        }

        return $results;
        // }}}
    }

    private function getReleasedVersions(): array
    {
        // {{{
        return array_filter(
            SplatoonVersion2::find()
                ->andWhere(['<', '{{splatoon_version2}}.[[released_at]]', new Now()])
                ->orderBy(['{{splatoon_version2}}.[[released_at]]' => SORT_ASC])
                ->all(),
            fn (SplatoonVersion2 $version): bool => !!version_compare($version->tag, '1.0', '>='),
        );
        // }}}
    }

    public function actionUpdateEntireSalmon2(): int
    {
        $this->updateEntireSalmon2();
        return 0;
    }

    public function actionUpdateEntireSalmon3(): int
    {
        $this->updateEntireSalmon3();
        return 0;
    }
}

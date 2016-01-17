<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use app\models\BattlePlayer;
use app\models\Knockout;
use app\models\Rule;
use app\models\StatEntireUser;
use app\models\StatWeapon;
use app\models\StatWeaponBattleCount;
use app\models\StatWeaponKillDeath;

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
                ['not like', '{{lobby}}.[[key]]', 'squad_%', false],
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
                        ])
                        ->groupBy(['{{battle}}.[[map_id]]', '{{battle}}.[[rule_id]]'])
                        ->createCommand()
                        ->queryAll()
                )
            )
            ->execute();
        $transaction->commit();
    }
}

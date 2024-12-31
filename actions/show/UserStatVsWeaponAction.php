<?php

/**
 * @copyright Copyright (C) 2016-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\show;

use Yii;
use app\models\BattleFilterForm;
use app\models\DeathReason;
use app\models\User;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;

use function array_map;
use function implode;
use function is_int;
use function sprintf;
use function strcasecmp;
use function uasort;

class UserStatVsWeaponAction extends BaseAction
{
    use UserStatFilterTrait;

    public $user;
    public $filter;

    public function init()
    {
        parent::init();

        $request = Yii::$app->getRequest();
        $this->user = User::findOne(['screen_name' => $request->get('screen_name')]);
        if (!$this->user) {
            throw new NotFoundHttpException(Yii::t('app', 'Could not find user'));
        }

        $this->filter = new BattleFilterForm();
        $this->filter->load($_GET);
        $this->filter->screen_name = $this->user->screen_name;
        $this->filter->validate();
    }

    public function run()
    {
        return Yii::$app->db->transaction(
            function (): string {
                $this->createTemporaryTables();
                $data = array_map(
                    function (array $data): array {
                        $battles = $data['battles'] ?? null;
                        $wins = $data['wins'] ?? null;
                        $data['win_pct'] = is_int($battles) && is_int($wins) && $battles > 0
                            ? 100 * $wins / $battles
                            : null;

                        $deaths = $data['deaths'] ?? null;
                        $data['deaths_per_game'] = is_int($battles) && is_int($deaths) && $battles > 0
                            ? $deaths / $battles
                            : null;
                        return $data;
                    },
                    ArrayHelper::merge($this->queryVersus(), $this->queryDeath()),
                );
                uasort($data, function (array $a, array $b): int {
                    if ($a['win_pct'] === null) {
                        if ($b['win_pct'] === null) {
                            return strcasecmp($a['weapon_name'], $b['weapon_name']);
                        }
                        return 1;
                    }
                    if ($b['win_pct'] === null) {
                        return -1;
                    }
                    return $b['win_pct'] <=> $a['win_pct']
                        ?: strcasecmp($a['weapon_name'], $b['weapon_name']);
                });

                return $this->controller->render('user-stat-vs-weapon', [
                    'user' => $this->user,
                    'filter' => $this->filter,
                    'data' => $data,
                ]);
            },
        );
    }

    protected function createTemporaryTables()
    {
        $this->createTemporaryTableDeathData();
        $this->createTemporaryTableBattle();
    }

    protected function createTemporaryTableDeathData()
    {
        $db = Yii::$app->db;
        $db->createCommand(sprintf(
            'CREATE TEMPORARY TABLE tmp_death_data (%s) ON COMMIT DROP',
            implode(', ', [
                'battle_id BIGINT NOT NULL PRIMARY KEY',
                'death BIGINT NOT NULL',
            ]),
        ))->execute();

        $db->createCommand(sprintf(
            'INSERT INTO tmp_death_data (battle_id, death) %s',
            (new Query())
                ->select([
                    'battle_id' => 'battle.id',
                    'death' => 'SUM(battle_death_reason.count)',
                ])
                ->from('battle')
                ->innerJoin('battle_death_reason', 'battle.id = battle_death_reason.battle_id')
                ->andWhere(['battle.user_id' => $this->user->id])
                ->groupBy('battle.id')
                ->createCommand()
                ->rawSql,
        ))->execute();
    }

    protected function createTemporaryTableBattle()
    {
        $db = Yii::$app->db;
        $db->createCommand(sprintf(
            'CREATE TEMPORARY TABLE tmp_battle (%s) ON COMMIT DROP',
            implode(', ', [
                'battle_id BIGINT NOT NULL PRIMARY KEY',
            ]),
        ))->execute();

        $query = (new Query())
            ->distinct()
            ->select([
                'battle_id' => '{{battle}}.[[id]]',
            ])
            ->from('battle')
            ->innerJoin('battle_player', implode(' AND ', [
                '{{battle}}.[[id]] = {{battle_player}}.[[battle_id]]',
                '{{battle_player}}.[[is_me]] = TRUE',
            ]))
            ->leftJoin('tmp_death_data', '{{battle}}.[[id]] = {{tmp_death_data}}.[[battle_id]]')
            ->andWhere(['and',
                ['{{battle}}.[[user_id]]' => $this->user->id],
                ['not', ['{{battle}}.[[is_win]]' => null]],
                ['or',
                    ['{{battle}}.[[death]]' => 0],
                    ['not', ['{{tmp_death_data}}.[[battle_id]]' => null]],
                ],
            ]);
        if (!$this->filter->hasErrors()) {
            $query
                ->leftJoin('rule', '{{battle}}.[[rule_id]] = {{rule}}.[[id]]')
                ->leftJoin('game_mode', '{{rule}}.[[mode_id]] = {{game_mode}}.[[id]]')
                ->leftJoin('lobby', '{{battle}}.[[lobby_id]] = {{lobby}}.[[id]]')
                ->leftJoin('map', '{{battle}}.[[map_id]] = {{map}}.[[id]]')
                ->leftJoin('weapon', '{{battle}}.[[weapon_id]] = {{weapon}}.[[id]]')
                ->leftJoin('weapon_type', '{{weapon}}.[[type_id]] = {{weapon_type}}.[[id]]')
                ->leftJoin('subweapon', '{{weapon}}.[[subweapon_id]] = {{subweapon}}.[[id]]')
                ->leftJoin('special', '{{weapon}}.[[special_id]] = {{special}}.[[id]]')
                ->leftJoin('rank', '{{battle}}.[[rank_id]] = {{rank}}.[[id]]')
                ->leftJoin('rank_group', '{{rank}}.[[group_id]] = {{rank_group}}.[[id]]');
            $this->filter($query, $this->filter);
        }

        $db->createCommand(sprintf(
            'INSERT INTO tmp_battle (battle_id) %s',
            $query->createCommand()->rawSql,
        ))->execute();
    }

    protected function queryVersus()
    {
        $db = Yii::$app->db;
        $ret = [];
        $query = (new Query())
            ->select([
                'weapon_key' => 'MAX({{weapon}}.[[key]])',
                'weapon_name' => 'MAX({{weapon}}.[[name]])',
                'sub_key' => 'MAX({{subweapon}}.[[key]])',
                'sub_name' => 'MAX({{subweapon}}.[[name]])',
                'special_key' => 'MAX({{special}}.[[key]])',
                'special_name' => 'MAX({{special}}.[[name]])',
                'battles' => 'COUNT(*)',
                'wins' => 'SUM(CASE WHEN {{battle}}.[[is_win]] THEN 1 ELSE 0 END)',
            ])
            ->from('tmp_battle')
            ->innerJoin(
                'battle_player',
                '{{tmp_battle}}.[[battle_id]] = {{battle_player}}.[[battle_id]]',
            )
            ->innerJoin('weapon', '{{battle_player}}.[[weapon_id]] = {{weapon}}.[[id]]')
            ->innerJoin('subweapon', '{{weapon}}.[[subweapon_id]] = {{subweapon}}.[[id]]')
            ->innerJoin('special', '{{weapon}}.[[special_id]] = {{special}}.[[id]]')
            ->innerJoin('battle', '{{tmp_battle}}.[[battle_id]] = {{battle}}.[[id]]')
            ->andWhere('{{battle_player}}.[[is_my_team]] = FALSE')
            ->groupBy('{{battle_player}}.[[weapon_id]]');
        foreach ($query->createCommand($db)->queryAll() as $row) {
            $row['weapon_name'] = Yii::t('app-weapon', $row['weapon_name']);
            $ret[$row['weapon_key']] = $row;

            if (!isset($ret[$row['sub_key']])) {
                $ret[$row['sub_key']] = [
                    'weapon_key' => $row['sub_key'],
                    'weapon_name' => Yii::t('app-subweapon', $row['sub_name']),
                    'battles' => $row['battles'],
                    'wins' => $row['wins'],
                ];
            } else {
                $ret[$row['sub_key']]['battles'] += $row['battles'];
                $ret[$row['sub_key']]['wins'] += $row['wins'];
            }

            if (!isset($ret[$row['special_key']])) {
                $ret[$row['special_key']] = [
                    'weapon_key' => $row['special_key'],
                    'weapon_name' => Yii::t('app-special', $row['special_name']),
                    'battles' => $row['battles'],
                    'wins' => $row['wins'],
                ];
            } else {
                $ret[$row['special_key']]['battles'] += $row['battles'];
                $ret[$row['special_key']]['wins'] += $row['wins'];
            }
        }
        return $ret;
    }

    protected function queryDeath()
    {
        $names = [];
        foreach (DeathReason::find()->all() as $obj) {
            $names[$obj->key] = $obj->getTranslatedName();
        }

        $db = Yii::$app->db;
        $ret = [];
        $query = (new Query())
            ->select([
                'weapon_key' => 'MAX({{death_reason}}.[[key]])',
                'deaths' => 'SUM({{battle_death_reason}}.[[count]])',
            ])
            ->from('tmp_battle')
            ->innerJoin(
                'battle_death_reason',
                '{{tmp_battle}}.[[battle_id]] = {{battle_death_reason}}.[[battle_id]]',
            )
            ->innerJoin(
                'death_reason',
                '{{battle_death_reason}}.[[reason_id]] = {{death_reason}}.[[id]]',
            )
            ->groupBy('{{battle_death_reason}}.[[reason_id]]');
        foreach ($query->createCommand($db)->queryAll() as $row) {
            $row['weapon_name'] = $names[$row['weapon_key']] ?? $row['weapon_key'];
            $ret[$row['weapon_key']] = $row;
        }
        return $ret;
    }
}

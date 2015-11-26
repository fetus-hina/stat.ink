<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\entire;

use Yii;
use yii\web\ViewAction as BaseAction;
use app\models\BattlePlayer;
use app\models\GameMode;
use app\models\Rule;
use app\models\Weapon;
use app\models\Subweapon;
use app\models\Special;

class WeaponsAction extends BaseAction
{
    public function run()
    {
        return $this->controller->render('weapons.tpl', [
            'entire' => $this->entireWeapons,
            'users' => $this->userWeapons,
        ]);
    }

    public function getEntireWeapons()
    {
        $rules = [];
        foreach (GameMode::find()->orderBy('id ASC')->all() as $mode) {
            $tmp = [];
            foreach ($mode->rules as $rule) {
                $weapons = $this->getEntireWeaponsByRule($rule);
                $tmp[] = (object)[
                    'key' => $rule->key,
                    'name' => Yii::t('app-rule', $rule->name),
                    'data' => $weapons,
                    'sub' => $this->convertWeapons2Sub($weapons),
                    'special' => $this->convertWeapons2Special($weapons),
                ];
            }
            usort($tmp, function ($a, $b) {
                return strnatcasecmp($a->name, $b->name);
            });
            while (!empty($tmp)) {
                $rules[] = array_shift($tmp);
            }
        }
        return $rules;
    }

    private function getEntireWeaponsByRule(Rule $rule)
    {
        $useCache = true;
        if ($useCache) {
            $cacheKey = hash('sha256', sprintf('%s(%s)', __METHOD__, $rule->key));
            if ($cache = Yii::$app->cache) {
                if (!$data = $cache->get($cacheKey)) {
                    $data = $this->getEntireWeaponsByRuleImpl($rule);
                    $cache->set($cacheKey, $data, 7200);
                }
                return $data;
            }
        }
        return $this->getEntireWeaponsByRuleImpl($rule);
    }

    private function getEntireWeaponsByRuleImpl(Rule $rule)
    {
        $query = BattlePlayer::find()
            ->innerJoinWith([
                'battle' => function ($q) {
                    return $q->orderBy(null);
                },
                'battle.lobby',
                'weapon',
                'weapon.subweapon',
                'weapon.special',
            ])
            ->andWhere(['{{battle}}.[[rule_id]]' => $rule->id])
            // プライベートバトルを除外
            ->andWhere(['<>', '{{lobby}}.[[key]]', 'private'])
            // 不完全っぽいデータを除外
            ->andWhere(['not', ['{{battle}}.[[is_win]]' => null]])
            ->andWhere(['not', ['{{battle_player}}.[[weapon_id]]' => null]])
            ->andWhere(['not', ['{{battle_player}}.[[kill]]' => null]])
            ->andWhere(['not', ['{{battle_player}}.[[death]]' => null]])
            // 自分は集計対象外（重複しまくる）
            ->andWhere(['{{battle_player}}.[[is_me]]' => false])
            ->groupBy('{{battle_player}}.[[weapon_id]]');

        // フェスマッチなら味方全部除外（連戦で無意味な重複の可能性が高い）
        // ナワバリは回線落ち判定ができるので回線落ちしたものは除外する
        // 厳密には全く塗らなかった人も除外されるが明らかに外れ値なので気にしない
        if ($rule->key === 'nawabari') {
            $query->andWhere(['or',
                [
                    '{{lobby}}.[[key]]' => 'standard',
                ],
                [
                    '{{lobby}}.[[key]]' => 'fest',
                    '{{battle_player}}.[[is_my_team]]' => false,
                ],
            ]);
            $query->andWhere(['not', ['{{battle_player}}.[[point]]' => null]]);
            $query->andWhere(['or',
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
            ]);
        }

        // タッグバトルなら味方全部除外（連戦で無意味な重複の可能性が高い）
        if (substr($rule->key, 0, 6) === 'squad_') {
            $query->andWhere(['{{battle_player}}.[[is_my_team]]' => false]);
        }

        $query->select([
            'key'           => 'MAX({{weapon}}.[[key]])',
            'name'          => 'MAX({{weapon}}.[[name]])',
            'sub_key'       => 'MAX({{subweapon}}.[[key]])',
            'sub'           => 'MAX({{subweapon}}.[[name]])',
            'special_key'   => 'MAX({{special}}.[[key]])',
            'special'       => 'MAX({{special}}.[[name]])',
            'count'         => 'COUNT(*)',
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
            'total_point' => $rule->key !== 'nawabari'
                ? '(0)'
                : sprintf(
                    'SUM(CASE %s END)',
                    implode(' ', [
                        'WHEN battle_player.point IS NULL THEN 0',
                        'WHEN battle.is_win = battle_player.is_my_team THEN battle_player.point - 300',
                        'ELSE battle_player.point',
                    ])
                ),
            'point_available' => $rule->key !== 'nawabari'
                ? '(0)'
                : sprintf(
                    'SUM(CASE %s END)',
                    implode(' ', [
                        'WHEN battle_player.point IS NULL THEN 0',
                        'ELSE 1',
                    ])
                ),
        ]);

        $list = array_map(function ($row) {
            return (object)[
                'key'       => $row['key'],
                'name'      => Yii::t('app-weapon', $row['name']),
                'subweapon' => (object)[
                    'key'   => $row['sub_key'],
                    'name'  => Yii::t('app-subweapon', $row['sub']),
                ],
                'special'   => (object)[
                    'key'   => $row['special_key'],
                    'name'  => Yii::t('app-special', $row['special']),
                ],
                'count'     => (int)$row['count'],
                'avg_kill'  => $row['count'] > 0 ? ($row['total_kill'] / $row['count']) : null,
                'sum_kill'  => $row['total_kill'],
                'avg_death' => $row['count'] > 0 ? ($row['total_death'] / $row['count']) : null,
                'sum_death' => $row['total_death'],
                'wp'        => $row['count'] > 0 ? ($row['win_count'] * 100 / $row['count']) : null,
                'win_count' => $row['win_count'],
                'avg_inked' => $row['point_available'] > 0 ? ($row['total_point'] / $row['point_available']) : null,
            ];
        }, $query->createCommand()->queryAll());

        usort($list, function ($a, $b) {
            foreach (['count', 'wp', 'avg_kill', 'avg_death'] as $key) {
                $tmp = $b->$key - $a->$key;
                if ($tmp != 0) {
                    return $tmp;
                }
            }
            return strnatcasecmp($a->name, $b->name);
        });

        $battleCount = $query
            ->select(['c' => 'COUNT(DISTINCT battle_id)'])
            ->groupBy(null)
            ->createCommand()
            ->queryScalar();

        return (object)[
            'battle_count' => $battleCount,
            'player_count' => array_sum(
                array_map(
                    function ($a) {
                        return $a->count;
                    },
                    $list
                )
            ),
            'weapons' => $list,
        ];
    }

    public function getUserWeapons()
    {
        $favWeaponQuery = (new \yii\db\Query())
            ->select('*')
            ->from('{{user_weapon}} AS {{m}}')
            ->andWhere([
                'not exists',
                (new \yii\db\Query())
                    ->select('(1)')
                    ->from('{{user_weapon}} AS {{s}}')
                    ->andWhere('{{m}}.[[user_id]] = {{s}}.[[user_id]]')
                    ->andWhere('{{m}}.[[count]] < {{s}}.[[count]]')
            ]);

        $query = (new \yii\db\Query())
            ->select(['weapon_id', 'count' => 'COUNT(*)'])
            ->from(sprintf(
                '(%s) AS {{tmp}}',
                $favWeaponQuery->createCommand()->rawSql
            ))
            ->groupBy('{{tmp}}.[[weapon_id]]')
            ->orderBy('COUNT(*) DESC');

        $list = $query->createCommand()->queryAll();
        $weapons = $this->getWeapons(array_map(function ($row) {
            return $row['weapon_id'];
        }, $list));

        return array_map(function ($row) use ($weapons) {
            return (object)[
                'weapon_id' => $row['weapon_id'],
                'user_count' => $row['count'],
                'weapon' => @$weapons[$row['weapon_id']] ?: null,
            ];
        }, $list);
    }

    public function getWeapons(array $weaponIdList)
    {
        $list = Weapon::find()
            ->andWhere(['in', '{{weapon}}.[[id]]', $weaponIdList])
            ->all();
        $ret = [];
        foreach ($list as $weapon) {
            $ret[$weapon->id] = $weapon;
        }
        return $ret;
    }

    private function convertWeapons2Sub($in)
    {
        $ret = [];
        foreach (Subweapon::find()->all() as $sub) {
            $ret[$sub->key] = (object)[
                'name'      => Yii::t('app-subweapon', $sub->name),
                'count'     => 0,
                'sum_kill'  => 0,
                'sum_death' => 0,
                'win_count' => 0,
                'avg_kill'  => null,
                'avg_death' => null,
                'wp'        => null,
                'encounter_3' => null,
                'encounter_4' => null,
            ];
        }
        foreach ($in->weapons as $weapon) {
            $o = $ret[$weapon->subweapon->key];
            $o->count     += $weapon->count;
            $o->sum_kill  += $weapon->sum_kill;
            $o->sum_death += $weapon->sum_death;
            $o->win_count += $weapon->win_count;
        }
        foreach ($ret as $o) {
            if ($o->count > 0) {
                $o->avg_kill  = $o->sum_kill / $o->count;
                $o->avg_death = $o->sum_death / $o->count;
                $o->wp = $o->win_count * 100 / $o->count;
                $encounterRate = $o->count / $in->player_count;
                $o->encounter_3 = 100 * (1 - pow(1 - $encounterRate, 3));
                $o->encounter_4 = 100 * (1 - pow(1 - $encounterRate, 4));
            }
        }

        usort($ret, function ($a, $b) {
            foreach (['count', 'wp', 'avg_kill', 'avg_death'] as $key) {
                $tmp = $b->$key - $a->$key;
                if ($tmp != 0) {
                    return $tmp;
                }
            }
            return strnatcasecmp($a->name, $b->name);
        });
        return $ret;
    }

    private function convertWeapons2Special($in)
    {
        $ret = [];
        foreach (Special::find()->all() as $spe) {
            $ret[$spe->key] = (object)[
                'name'      => Yii::t('app-special', $spe->name),
                'count'     => 0,
                'sum_kill'  => 0,
                'sum_death' => 0,
                'win_count' => 0,
                'avg_kill'  => null,
                'avg_death' => null,
                'wp'        => null,
                'encounter_3' => null,
                'encounter_4' => null,
            ];
        }
        foreach ($in->weapons as $weapon) {
            $o = $ret[$weapon->special->key];
            $o->count     += $weapon->count;
            $o->sum_kill  += $weapon->sum_kill;
            $o->sum_death += $weapon->sum_death;
            $o->win_count += $weapon->win_count;
        }
        foreach ($ret as $o) {
            if ($o->count > 0) {
                $o->avg_kill  = $o->sum_kill / $o->count;
                $o->avg_death = $o->sum_death / $o->count;
                $o->wp = $o->win_count * 100 / $o->count;
                $encounterRate = $o->count / $in->player_count;
                $o->encounter_3 = 100 * (1 - pow(1 - $encounterRate, 3));
                $o->encounter_4 = 100 * (1 - pow(1 - $encounterRate, 4));
                $o->encounter_r = $encounterRate * 100;
            }
        }

        usort($ret, function ($a, $b) {
            foreach (['count', 'wp', 'avg_kill', 'avg_death'] as $key) {
                $tmp = $b->$key - $a->$key;
                if ($tmp != 0) {
                    return $tmp;
                }
            }
            return strnatcasecmp($a->name, $b->name);
        });
        return $ret;
    }
}

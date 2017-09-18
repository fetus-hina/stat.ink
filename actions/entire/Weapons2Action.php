<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\entire;

use DateInterval;
use DateTime;
use DateTimeZone;
use Yii;
use app\models\Rule2;
use app\models\Special2;
use app\models\StatWeapon2UseCount;
use app\models\Subweapon2;
use app\models\Weapon2;
use yii\db\Query;
use yii\web\ViewAction as BaseAction;

class Weapons2Action extends BaseAction
{
    public function run()
    {
        return $this->controller->render('weapons2', [
            'uses' => $this->weaponUses,
            'entire' => $this->entireWeapons,
        ]);
    }

    public function getWeaponUses()
    {
        $threshold = (function () {
            $date = (new DateTime('@' . $_SERVER['REQUEST_TIME']))
                ->setTimezone(new DateTimeZone('Asia/Tokyo'))
                ->sub(new DateInterval('P1W'));
            return [
                (int)$date->format('o'), // isoyear
                (int)$date->format('W'), // isoweek
            ];
        })();

        // 最近よく使われているブキを抽出
        $qTrend = (new Query())
            ->select([
                'weapon_id',
                'battles' => 'SUM([[battles]])',
            ])
            ->from('stat_weapon2_use_count_per_week')
            ->where(['or',
                ['>', 'isoyear', $threshold[0]],
                ['and',
                    ['=', 'isoyear', $threshold[0]],
                    ['>=', 'isoweek', $threshold[1]],
                ]
            ])
            ->groupBy('weapon_id')
            ->orderBy([
                'SUM([[battles]])' => SORT_DESC,
            ])
            ->limit(15);
        if (!$trends = $qTrend->all()) {
            return [];
        }
        $query = (new Query())
            ->select(array_merge(
                ['isoyear', 'isoweek', 'battles' => 'SUM([[battles]])'],
                (function () use ($trends) {
                    $ret = [];
                    foreach ($trends as $trend) {
                        $key = sprintf('w%d', $trend['weapon_id']);
                        $ret[$key] = sprintf(
                            'SUM(CASE WHEN [[weapon_id]] = %d THEN [[battles]] ELSE 0 END)',
                            $trend['weapon_id']
                        );
                    }
                    return $ret;
                })()
            ))
            ->from('stat_weapon2_use_count_per_week')
            ->where(['or',
                ['>', 'isoyear', 2017],
                ['and',
                    ['=', 'isoyear', 2017],
                    ['>=', 'isoweek', 31],
                ]
            ])
            ->groupBy('isoyear, isoweek')
            ->orderBy('isoyear, isoweek');
        if (!$baselist = $query->all()) {
            return [];
        }

        $weapons = Weapon2::findAll([
            'id' => array_map(function ($_) {
                return $_['weapon_id'];
            }, $trends),
        ]);

        return array_map(function (array $_) use ($trends, $weapons) : array {
            $w = [];
            $total = 0;
            foreach ($trends as $trend) {
                $key = 'w' . $trend['weapon_id'];
                $count = (int)$_[$key];
                $weapon = (function ($id) use ($weapons) {
                    foreach ($weapons as $weapon) {
                        if ($weapon->id == $id) {
                            return $weapon;
                        }
                    }
                    return null;
                })($trend['weapon_id']);
                
                $w[] = [
                    'name' => Yii::t('app-weapon2', $weapon->name ?? '?'),
                    'battles' => $count,
                    'pct' => $_['battles'] > 0 ? ($count * 100 / $_['battles']) : null,
                ];
                $total += $count;
            }
            return [
                'date' => date('Y-m-d', strtotime(sprintf('%04d-W%02d', $_['isoyear'], $_['isoweek']))),
                'battles' => (int)$_['battles'],
                'weapons' => $w,
                'others' => $_['battles'] - $total,
                'others_pct' => $_['battles'] > 0 ? (($_['battles'] - $total) * 100 / $_['battles']) : null,
            ];
        }, $baselist);
    }

    public function getEntireWeapons() : array
    {
        $rules = [];
        foreach (Rule2::find()->orderBy(['id' => SORT_ASC])->all() as $rule) {
            $weapons = $this->getEntireWeaponsByRule($rule);
            $rules[] = (object)[
                'key' => $rule->key,
                'name' => Yii::t('app-rule2', $rule->name),
                'data' => $weapons,
                'sub' => $this->convertWeapons2Sub($weapons),
                'special' => $this->convertWeapons2Special($weapons),
            ];
        }
        return $rules;
    }

    private function getEntireWeaponsByRule(Rule2 $rule)
    {
        $columns = [
            'weapon_key'        => 'MAX({{weapon2}}.[[key]])',
            'weapon_name'       => 'MAX({{weapon2}}.[[name]])',
            'subweapon_key'     => 'MAX({{subweapon2}}.[[key]])',
            'subweapon_name'    => 'MAX({{subweapon2}}.[[name]])',
            'special_key'       => 'MAX({{special2}}.[[key]])',
            'special_name'      => 'MAX({{special2}}.[[name]])',
            'count'             => 'SUM({{stat_weapon2_use_count}}.[[battles]])',
            'avg_kill'          => sprintf(
                '(%s / NULLIF(%s, 0))',
                'SUM({{stat_weapon2_use_count}}.[[kills]])',
                'SUM({{stat_weapon2_use_count}}.[[kd_available]])'
            ),
            'sum_kill'          => 'SUM({{stat_weapon2_use_count}}.[[kills]])',
            'kill_per_min'      => sprintf(
                '(%s * 60.0 / NULLIF(%s, 0))',
                'SUM({{stat_weapon2_use_count}}.[[kills_with_time]])',
                'SUM({{stat_weapon2_use_count}}.[[kd_time_seconds]])'
            ),
            'avg_death'         => sprintf(
                '(%s / NULLIF(%s, 0))',
                'SUM({{stat_weapon2_use_count}}.[[deaths]])',
                'SUM({{stat_weapon2_use_count}}.[[kd_available]])'
            ),
            'sum_death'         => 'SUM({{stat_weapon2_use_count}}.[[deaths]])',
            'death_per_min'     => sprintf(
                '(%s * 60.0 / NULLIF(%s, 0))',
                'SUM({{stat_weapon2_use_count}}.[[deaths_with_time]])',
                'SUM({{stat_weapon2_use_count}}.[[kd_time_seconds]])'
            ),
            'avg_special'       => sprintf(
                '(%s / NULLIF(%s, 0))',
                'SUM({{stat_weapon2_use_count}}.[[specials]])',
                'SUM({{stat_weapon2_use_count}}.[[specials_available]])'
            ),
            'sum_special'       => 'SUM({{stat_weapon2_use_count}}.[[specials]])',
            'special_per_min'   => sprintf(
                '(%s * 60.0 / NULLIF(%s, 0))',
                'SUM({{stat_weapon2_use_count}}.[[specials_with_time]])',
                'SUM({{stat_weapon2_use_count}}.[[specials_time_seconds]])'
            ),
            'avg_inked'         => sprintf(
                '(%s / NULLIF(%s, 0))',
                'SUM({{stat_weapon2_use_count}}.[[inked]])',
                'SUM({{stat_weapon2_use_count}}.[[inked_available]])'
            ),
            'sum_inked'         => 'SUM({{stat_weapon2_use_count}}.[[inked]])',
            'inked_per_min'     => sprintf(
                '(%s * 60.0 / NULLIF(%s, 0))',
                'SUM({{stat_weapon2_use_count}}.[[inked_with_time]])',
                'SUM({{stat_weapon2_use_count}}.[[inked_time_seconds]])'
            ),
            'wp'                => sprintf(
                '(%s * 100.0 / NULLIF(%s, 0))',
                'SUM({{stat_weapon2_use_count}}.[[wins]])',
                'SUM({{stat_weapon2_use_count}}.[[battles]])'
            ),
            'win_count'         => 'SUM({{stat_weapon2_use_count}}.[[wins]])',
        ];
        $query = StatWeapon2UseCount::find()
            ->select($columns)
            ->innerJoinWith([
                'weapon',
                'weapon.subweapon',
                'weapon.special',
            ])
            ->andWhere(['{{stat_weapon2_use_count}}.[[rule_id]]' => $rule->id])
            ->groupBy('{{stat_weapon2_use_count}}.[[weapon_id]]');

        $totalPlayers = 0;
        $list = array_map(
            function ($model) use (&$totalPlayers) {
                $totalPlayers += $model['count'];
                return (object)[
                    'key'       => $model['weapon_key'],
                    'name'      => Yii::t('app-weapon2', $model['weapon_name']),
                    'subweapon' => (object)[
                        'key'   => $model['subweapon_key'],
                        'name'  => Yii::t('app-subweapon2', $model['subweapon_name']),
                    ],
                    'special'   => (object)[
                        'key'   => $model['special_key'],
                        'name'  => Yii::t('app-special2', $model['special_name']),
                    ],
                    'count'     => (int)$model['count'],
                    'avg_kill'  => (float)$model['avg_kill'],
                    'sum_kill'  => (int)$model['sum_kill'],
                    'kill_per_min' => (float)$model['kill_per_min'],
                    'avg_death' => (float)$model['avg_death'],
                    'sum_death' => (int)$model['sum_death'],
                    'death_per_min' => (float)$model['death_per_min'],
                    'kill_ratio' => ($model['avg_death'] == 0)
                        ? ($model['avg_kill'] == 0 ? null : 99.99)
                        : ($model['avg_kill'] / $model['avg_death']),
                    'wp'        => (float)$model['wp'],
                    'win_count' => (int)$model['win_count'],
                    'avg_inked' => (float)$model['avg_inked'],
                    'inked_per_min' => (float)$model['inked_per_min'],
                    'avg_special' => (float)$model['avg_special'],
                    'special_per_min' => (float)$model['special_per_min'],
                    'sum_special' => (int)$model['sum_special'],
                ];
            },
            $query->createCommand()->queryAll()
        );

        usort($list, function ($a, $b) {
            foreach (['count', 'wp', 'avg_kill', 'avg_death'] as $key) {
                $tmp = $b->$key - $a->$key;
                if ($tmp != 0) {
                    return $tmp;
                }
            }
            return strnatcasecmp($a->name, $b->name);
        });

        return (object)[
            'player_count' => $totalPlayers,
            'weapons' => $list,
        ];
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
        foreach (Subweapon2::find()->all() as $sub) {
            $ret[$sub->key] = (object)[
                'name'      => Yii::t('app-subweapon2', $sub->name),
                'count'     => 0,
                'sum_kill'  => 0,
                'sum_death' => 0,
                'sum_special' => 0,
                'win_count' => 0,
                'avg_kill'  => null,
                'avg_death' => null,
                'avg_special' => null,
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
            $o->sum_special += $weapon->sum_special;
            $o->win_count += $weapon->win_count;
        }
        foreach ($ret as $o) {
            if ($o->count > 0) {
                $o->avg_kill  = $o->sum_kill / $o->count;
                $o->avg_death = $o->sum_death / $o->count;
                $o->avg_special = $o->sum_special / $o->count;
                $o->wp = $o->win_count * 100 / $o->count;
                $encounterRate = $o->count / $in->player_count;
                $o->encounter_3 = 100 * (1 - pow(1 - $encounterRate, 3));
                $o->encounter_4 = 100 * (1 - pow(1 - $encounterRate, 4));
                if ($o->sum_death == 0) {
                    $o->kill_ratio = ($o->sum_kill > 0 ? 99.99 : null);
                } else {
                    $o->kill_ratio = $o->sum_kill / $o->sum_death;
                }
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
        foreach (Special2::find()->all() as $spe) {
            $ret[$spe->key] = (object)[
                'name'      => Yii::t('app-special2', $spe->name),
                'count'     => 0,
                'sum_kill'  => 0,
                'sum_death' => 0,
                'sum_special' => 0,
                'win_count' => 0,
                'avg_kill'  => null,
                'avg_death' => null,
                'avg_special' => null,
                'wp'        => null,
                'encounter_3' => null,
                'encounter_4' => null,
                'kill_ratio' => null,
            ];
        }
        foreach ($in->weapons as $weapon) {
            $o = $ret[$weapon->special->key];
            $o->count     += $weapon->count;
            $o->sum_kill  += $weapon->sum_kill;
            $o->sum_death += $weapon->sum_death;
            $o->sum_special += $weapon->sum_special;
            $o->win_count += $weapon->win_count;
        }
        foreach ($ret as $o) {
            if ($o->count > 0) {
                $o->avg_kill  = $o->sum_kill / $o->count;
                $o->avg_death = $o->sum_death / $o->count;
                $o->avg_special = $o->sum_special / $o->count;
                $o->wp = $o->win_count * 100 / $o->count;
                $encounterRate = $o->count / $in->player_count;
                $o->encounter_3 = 100 * (1 - pow(1 - $encounterRate, 3));
                $o->encounter_4 = 100 * (1 - pow(1 - $encounterRate, 4));
                $o->encounter_r = $encounterRate * 100;
                if ($o->sum_death == 0) {
                    $o->kill_ratio = ($o->sum_kill > 0 ? 99.99 : null);
                } else {
                    $o->kill_ratio = $o->sum_kill / $o->sum_death;
                }
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

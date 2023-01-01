<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\entire;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\components\helpers\Battle as BattleHelper;
use app\models\EntireWeapon2Form;
use app\models\Rule2;
use app\models\Special2;
use app\models\SplatoonVersion2;
use app\models\SplatoonVersionGroup2;
use app\models\StatWeapon2UseCount;
use app\models\Subweapon2;
use app\models\Weapon2;
use stdClass;
use yii\db\Query;
use yii\web\ViewAction as BaseAction;

class Weapons2Action extends BaseAction
{
    public function run()
    {
        $form = Yii::createObject(['class' => EntireWeapon2Form::class]);
        $form->load($_GET) && $form->validate();

        return $this->controller->render('weapons2', [
            'form' => $form,
            'uses' => $this->weaponUses,
            'entire' => $this->getEntireWeapons($form),
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
                ],
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
                            $trend['weapon_id'],
                        );
                    }
                    return $ret;
                })(),
            ))
            ->from('stat_weapon2_use_count_per_week')
            ->where(['or',
                ['>', 'isoyear', 2017],
                ['and',
                    ['=', 'isoyear', 2017],
                    ['>=', 'isoweek', 31],
                ],
            ])
            ->groupBy('isoyear, isoweek')
            ->orderBy('isoyear, isoweek');
        if (!$baselist = $query->all()) {
            return [];
        }

        $weapons = Weapon2::findAll([
            'id' => array_map(fn ($_) => $_['weapon_id'], $trends),
        ]);

        return array_map(function (array $_) use ($trends, $weapons): array {
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

    public function getEntireWeapons(EntireWeapon2Form $form): array
    {
        $rules = [];
        foreach (Rule2::find()->orderBy(['id' => SORT_ASC])->all() as $rule) {
            $weapons = $this->getEntireWeaponsByRule($rule, $form);
            $rules[] = (object)[
                'key' => $rule->key,
                'name' => Yii::t('app-rule2', $rule->name),
                'data' => $weapons,
                'sub' => $this->convertWeapons2Sub($weapons),
                'special' => $this->convertWeapons2Special($weapons),
                'type' => $this->convertWeapons2Type($weapons),
                'category' => $this->convertWeapons2Category($weapons),
            ];
        }
        return $rules;
    }

    private function getEntireWeaponsByRule(Rule2 $rule, EntireWeapon2Form $form)
    {
        $columns = [
            'weapon_key' => 'MAX({{weapon2}}.[[key]])',
            'weapon_name' => 'MAX({{weapon2}}.[[name]])',
            'subweapon_key' => 'MAX({{subweapon2}}.[[key]])',
            'subweapon_name' => 'MAX({{subweapon2}}.[[name]])',
            'special_key' => 'MAX({{special2}}.[[key]])',
            'special_name' => 'MAX({{special2}}.[[name]])',
            'type_key' => 'MAX({{weapon_type2}}.[[key]])',
            'type_name' => 'MAX({{weapon_type2}}.[[name]])',
            'category_key' => 'MAX({{weapon_category2}}.[[key]])',
            'category_name' => 'MAX({{weapon_category2}}.[[name]])',
            'count' => 'SUM({{stat_weapon2_use_count}}.[[battles]])',
            'avg_kill' => sprintf(
                '(%s / NULLIF(%s, 0))',
                'SUM({{stat_weapon2_use_count}}.[[kills]])',
                'SUM({{stat_weapon2_use_count}}.[[kd_available]])',
            ),
            'sum_kill' => 'SUM({{stat_weapon2_use_count}}.[[kills]])',
            'kill_per_min' => sprintf(
                '(%s * 60.0 / NULLIF(%s, 0))',
                'SUM({{stat_weapon2_use_count}}.[[kills_with_time]])',
                'SUM({{stat_weapon2_use_count}}.[[kd_time_seconds]])',
            ),
            'avg_death' => sprintf(
                '(%s / NULLIF(%s, 0))',
                'SUM({{stat_weapon2_use_count}}.[[deaths]])',
                'SUM({{stat_weapon2_use_count}}.[[kd_available]])',
            ),
            'sum_death' => 'SUM({{stat_weapon2_use_count}}.[[deaths]])',
            'death_per_min' => sprintf(
                '(%s * 60.0 / NULLIF(%s, 0))',
                'SUM({{stat_weapon2_use_count}}.[[deaths_with_time]])',
                'SUM({{stat_weapon2_use_count}}.[[kd_time_seconds]])',
            ),
            'avg_special' => sprintf(
                '(%s / NULLIF(%s, 0))',
                'SUM({{stat_weapon2_use_count}}.[[specials]])',
                'SUM({{stat_weapon2_use_count}}.[[specials_available]])',
            ),
            'sum_special' => 'SUM({{stat_weapon2_use_count}}.[[specials]])',
            'special_per_min' => sprintf(
                '(%s * 60.0 / NULLIF(%s, 0))',
                'SUM({{stat_weapon2_use_count}}.[[specials_with_time]])',
                'SUM({{stat_weapon2_use_count}}.[[specials_time_seconds]])',
            ),
            'avg_inked' => sprintf(
                '(%s / NULLIF(%s, 0))',
                'SUM({{stat_weapon2_use_count}}.[[inked]])',
                'SUM({{stat_weapon2_use_count}}.[[inked_available]])',
            ),
            'sum_inked' => 'SUM({{stat_weapon2_use_count}}.[[inked]])',
            'inked_per_min' => sprintf(
                '(%s * 60.0 / NULLIF(%s, 0))',
                'SUM({{stat_weapon2_use_count}}.[[inked_with_time]])',
                'SUM({{stat_weapon2_use_count}}.[[inked_time_seconds]])',
            ),
            'wp' => sprintf(
                '(%s * 100.0 / NULLIF(%s, 0))',
                'SUM({{stat_weapon2_use_count}}.[[wins]])',
                'SUM({{stat_weapon2_use_count}}.[[battles]])',
            ),
            'win_count' => 'SUM({{stat_weapon2_use_count}}.[[wins]])',
        ];
        $query = StatWeapon2UseCount::find()
            ->select($columns)
            ->innerJoinWith([
                'weapon',
                'weapon.subweapon',
                'weapon.special',
                'weapon.type',
                'weapon.type.category',
            ])
            ->andWhere(['{{stat_weapon2_use_count}}.[[rule_id]]' => $rule->id])
            ->groupBy('{{stat_weapon2_use_count}}.[[weapon_id]]');
        try {
            if ($form->hasErrors()) {
                throw new \Exception();
            }
            if ($form->map != '') {
                $query->innerJoinWith('map');
                $query->andWhere([
                    '{{map2}}.[[key]]' => $form->map,
                ]);
            }
            if ($form->term == '') {
                // nothing to do
            } elseif (preg_match('/^(\d{4})-(\d{2})$/', $form->term, $match)) {
                // [$start, $end)
                $start = (new DateTimeImmutable())
                    ->setTimeZone(new DateTimeZone('Etc/UTC'))
                    ->setDate(intval($match[1], 10), intval($match[2], 10), 1)
                    ->setTime(0, 0, 0);
                $end = $start->add(new DateInterval('P1M'));
                $startPeriod = BattleHelper::calcPeriod2($start->getTimestamp());
                $endPeriod = BattleHelper::calcPeriod2($end->getTimestamp());
                $query->andWhere(['and',
                    ['>=', '{{stat_weapon2_use_count}}.[[period]]', $startPeriod],
                    ['<', '{{stat_weapon2_use_count}}.[[period]]', $endPeriod],
                ]);
            } elseif (substr($form->term, 0, 1) === 'v') {
                if (!$v1 = SplatoonVersion2::findOne(['tag' => substr($form->term, 1)])) {
                    throw new \Exception();
                }
                $v2 = SplatoonVersion2::find()
                    ->andWhere(['>', 'released_at', $v1->released_at])
                    ->orderBy(['released_at' => SORT_ASC])
                    ->limit(1)
                    ->one();
                $query->andWhere([
                    '>=',
                    '{{stat_weapon2_use_count}}.[[period]]',
                    BattleHelper::calcPeriod2(strtotime($v1->released_at)),
                ]);
                if ($v2) {
                    $query->andWhere([
                        '<',
                        '{{stat_weapon2_use_count}}.[[period]]',
                        BattleHelper::calcPeriod2(strtotime($v2->released_at)),
                    ]);
                }
            } elseif (substr($form->term, 0, 2) === '~v') {
                if (!$vg = SplatoonVersionGroup2::findOne(['tag' => substr($form->term, 2)])) {
                    throw new \Exception();
                }

                $versions = SplatoonVersion2::find()
                    ->andWhere(['group_id' => $vg->id])
                    ->orderBy(['released_at' => SORT_ASC])
                    ->all();
                if (!$versions) {
                    throw new \Exception();
                }
                $v1 = $versions[0];
                $v2 = $versions[count($versions) - 1];
                $v3 = SplatoonVersion2::find()
                    ->andWhere(['>', 'released_at', $v2->released_at])
                    ->orderBy(['released_at' => SORT_ASC])
                    ->limit(1)
                    ->one();
                $query->andWhere([
                    '>=',
                    '{{stat_weapon2_use_count}}.[[period]]',
                    BattleHelper::calcPeriod2(strtotime($v1->released_at)),
                ]);
                if ($v3) {
                    $query->andWhere([
                        '<',
                        '{{stat_weapon2_use_count}}.[[period]]',
                        BattleHelper::calcPeriod2(strtotime($v3->released_at)),
                    ]);
                }
            } else {
                throw new \Exception();
            }
        } catch (\Throwable $e) {
            $query->andWhere('0 = 1');
        }

        $totalPlayers = 0;
        $list = array_map(
            function ($model) use (&$totalPlayers) {
                $totalPlayers += $model['count'];
                return (object)[
                    'key' => $model['weapon_key'],
                    'name' => Yii::t('app-weapon2', $model['weapon_name']),
                    'subweapon' => (object)[
                        'key' => $model['subweapon_key'],
                        'name' => Yii::t('app-subweapon2', $model['subweapon_name']),
                    ],
                    'special' => (object)[
                        'key' => $model['special_key'],
                        'name' => Yii::t('app-special2', $model['special_name']),
                    ],
                    'type' => (object)[
                        'key' => $model['type_key'],
                        'name' => Yii::t('app-weapon2', $model['type_name']),
                    ],
                    'category' => (object)[
                        'key' => $model['category_key'],
                        'name' => Yii::t('app-weapon2', $model['category_name']),
                    ],
                    'count' => (int)$model['count'],
                    'avg_kill' => (float)$model['avg_kill'],
                    'sum_kill' => (int)$model['sum_kill'],
                    'kill_per_min' => (float)$model['kill_per_min'],
                    'avg_death' => (float)$model['avg_death'],
                    'sum_death' => (int)$model['sum_death'],
                    'death_per_min' => (float)$model['death_per_min'],
                    'kill_ratio' => $model['avg_death'] == 0
                        ? ($model['avg_kill'] == 0 ? null : 99.99)
                        : $model['avg_kill'] / $model['avg_death'],
                    'wp' => (float)$model['wp'],
                    'win_count' => (int)$model['win_count'],
                    'avg_inked' => (float)$model['avg_inked'],
                    'inked_per_min' => (float)$model['inked_per_min'],
                    'avg_special' => (float)$model['avg_special'],
                    'special_per_min' => (float)$model['special_per_min'],
                    'sum_special' => (int)$model['sum_special'],
                    'ink_performance' => $model['avg_death'] == 0
                        ? null
                        : (float)$model['avg_inked'] / (9 * (20 - (float)$model['avg_death'])),
                ];
            },
            $query->createCommand()->queryAll(),
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
        $list = Weapon2::find()
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
        static $subs = null;
        if ($subs === null) {
            $subs = Subweapon2::find()->all();
        }

        $ret = [];
        foreach ($subs as $sub) {
            $ret[$sub->key] = (object)[
                'name' => Yii::t('app-subweapon2', $sub->name),
                'key' => $sub->key,
                'count' => 0,
                'sum_kill' => 0,
                'sum_death' => 0,
                'sum_special' => 0,
                'win_count' => 0,
                'avg_kill' => null,
                'avg_death' => null,
                'avg_special' => null,
                'wp' => null,
                'encounter_3' => null,
                'encounter_4' => null,
            ];
        }
        foreach ($in->weapons as $weapon) {
            $o = $ret[$weapon->subweapon->key];
            $o->count += $weapon->count;
            $o->sum_kill += $weapon->sum_kill;
            $o->sum_death += $weapon->sum_death;
            $o->sum_special += $weapon->sum_special;
            $o->win_count += $weapon->win_count;
        }
        foreach ($ret as $o) {
            if ($o->count > 0) {
                $o->avg_kill = $o->sum_kill / $o->count;
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

        usort($ret, fn (stdClass $a, stdClass $b): int => $b->count <=> $a->count
                ?: $b->wp <=> $a->wp
                ?: $b->avg_kill <=> $a->avg_kill
                ?: $b->avg_death <=> $b->avg_death
                ?: strnatcasecmp($a->name, $b->name));
        return $ret;
    }

    private function convertWeapons2Special($in)
    {
        static $specials = null;
        if ($specials === null) {
            $specials = Special2::find()->all();
        }

        $ret = [];
        foreach ($specials as $spe) {
            $ret[$spe->key] = (object)[
                'name' => Yii::t('app-special2', $spe->name),
                'key' => $spe->key,
                'count' => 0,
                'sum_kill' => 0,
                'sum_death' => 0,
                'sum_special' => 0,
                'win_count' => 0,
                'avg_kill' => null,
                'avg_death' => null,
                'avg_special' => null,
                'wp' => null,
                'encounter_3' => null,
                'encounter_4' => null,
                'kill_ratio' => null,
            ];
        }
        foreach ($in->weapons as $weapon) {
            $o = $ret[$weapon->special->key];
            $o->count += $weapon->count;
            $o->sum_kill += $weapon->sum_kill;
            $o->sum_death += $weapon->sum_death;
            $o->sum_special += $weapon->sum_special;
            $o->win_count += $weapon->win_count;
        }
        foreach ($ret as $o) {
            if ($o->count > 0) {
                $o->avg_kill = $o->sum_kill / $o->count;
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

        usort($ret, fn (stdClass $a, stdClass $b): int => $b->count <=> $a->count
                ?: $b->wp <=> $a->wp
                ?: $b->avg_kill <=> $a->avg_kill
                ?: $b->avg_death <=> $b->avg_death
                ?: strnatcasecmp($a->name, $b->name));
        return $ret;
    }

    private function convertWeapons2Type(\stdClass $in): array
    {
        $weapons = $in->weapons;
        $mergeKeys = [
            'count',
            'sum_kill',
            'sum_death',
            'sum_special',
            'win_count',
        ];
        $ret = [];
        foreach ($weapons as $weapon) {
            if (!isset($ret[$weapon->type->key])) {
                $tmp = clone $weapon;
                $tmp->key = $weapon->type->key;
                $tmp->name = $weapon->type->name;
                unset($tmp->subweapon);
                unset($tmp->special);
                unset($tmp->type);
                unset($tmp->category);
                $ret[$weapon->type->key] = $tmp;
            } else {
                $tmp = $ret[$weapon->type->key];
                foreach ($mergeKeys as $key) {
                    $tmp->$key += $weapon->$key;
                }

                if ($tmp->count > 0) {
                    $tmp->wp = $tmp->win_count * 100 / $tmp->count;
                    $tmp->avg_kill = $tmp->sum_kill / $tmp->count;
                    $tmp->avg_death = $tmp->sum_death / $tmp->count;
                    $tmp->avg_special = $tmp->sum_special / $tmp->count;
                }
            }
        }

        usort($ret, fn (stdClass $a, stdClass $b): int => $b->count <=> $a->count
                ?: $b->wp <=> $a->wp
                ?: $b->avg_kill <=> $a->avg_kill
                ?: $b->avg_death <=> $b->avg_death
                ?: strnatcasecmp($a->name, $b->name));
        return $ret;
    }

    private function convertWeapons2Category(\stdClass $in): array
    {
        $weapons = $in->weapons;
        $mergeKeys = [
            'count',
            'sum_kill',
            'sum_death',
            'sum_special',
            'win_count',
        ];
        $ret = [];
        foreach ($weapons as $weapon) {
            if (!isset($ret[$weapon->category->key])) {
                $tmp = clone $weapon;
                $tmp->key = $weapon->category->key;
                $tmp->name = $weapon->category->name;
                unset($tmp->subweapon);
                unset($tmp->special);
                unset($tmp->type);
                unset($tmp->category);
                $ret[$weapon->category->key] = $tmp;
            } else {
                $tmp = $ret[$weapon->category->key];
                foreach ($mergeKeys as $key) {
                    $tmp->$key += $weapon->$key;
                }

                if ($tmp->count > 0) {
                    $tmp->wp = $tmp->win_count * 100 / $tmp->count;
                    $tmp->avg_kill = $tmp->sum_kill / $tmp->count;
                    $tmp->avg_death = $tmp->sum_death / $tmp->count;
                    $tmp->avg_special = $tmp->sum_special / $tmp->count;
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

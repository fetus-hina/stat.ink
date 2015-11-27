<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\entire;

use Yii;
use yii\web\ViewAction as BaseAction;
use app\models\GameMode;
use app\models\Rule;
use app\models\Weapon;
use app\models\Subweapon;
use app\models\Special;
use app\models\StatWeapon;
use app\models\StatWeaponBattleCount;

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
        $query = StatWeapon::find()
            ->with([
                'weapon',
                'weapon.subweapon',
                'weapon.special',
            ])
            ->andWhere(['{{stat_weapon}}.[[rule_id]]' => $rule->id]);

        $totalPlayers = 0;
        $list = array_map(
            function ($model) use (&$totalPlayers) {
                $totalPlayers += $model->players;
                return (object)[
                    'key'       => $model->weapon->key,
                    'name'      => Yii::t('app-weapon', $model->weapon->name),
                    'subweapon' => (object)[
                        'key'   => $model->weapon->subweapon->key,
                        'name'  => Yii::t('app-subweapon', $model->weapon->subweapon->name),
                    ],
                    'special'   => (object)[
                        'key'   => $model->weapon->special->key,
                        'name'  => Yii::t('app-special', $model->weapon->special->name),
                    ],
                    'count'     => (int)$model->players,
                    'avg_kill'  => $model->players > 0 ? ($model->total_kill / $model->players) : null,
                    'sum_kill'  => $model->total_kill,
                    'avg_death' => $model->players > 0 ? ($model->total_death / $model->players) : null,
                    'sum_death' => $model->total_death,
                    'wp'        => $model->players > 0 ? ($model->win_count * 100 / $model->players) : null,
                    'win_count' => $model->win_count,
                    'avg_inked' => $model->point_available > 0 ? ($model->total_point / $model->point_available) : null,
                ];
            },
            $query->all()
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

        $battleCount = StatWeaponBattleCount::findOne(['rule_id' => $rule->id]);

        return (object)[
            'battle_count' => $battleCount ? $battleCount->count : 0,
            'player_count' => $totalPlayers,
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

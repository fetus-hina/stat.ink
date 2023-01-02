<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\entire;

use Yii;
use app\models\BattleFilterForm;
use app\models\GameMode;
use app\models\Map;
use app\models\Rule;
use app\models\StatWeaponKDWinRate;
use app\models\WeaponType;
use stdClass;
use yii\db\Query;
use yii\web\ViewAction as BaseAction;

use function array_merge;
use function asort;
use function range;
use function strcmp;
use function substr;
use function usort;

class KDWinAction extends BaseAction
{
    public const KD_LIMIT = 16;

    public function run()
    {
        $filter = new BattleFilterForm();
        $filter->load($_GET);
        $filter->validate();

        $data = [];
        $modes = GameMode::find()->orderBy('id')->all();
        foreach ($modes as $mode) {
            $tmpData = [];
            foreach ($mode->rules as $rule) {
                $tmpData[] = (object)[
                    'key' => $rule->key,
                    'name' => Yii::t('app-rule', $rule->name),
                    'data' => $this->makeData($rule, $filter),
                ];
            }
            usort($tmpData, fn (stdClass $a, stdClass $b): int => strcmp($a->name, $b->name));
            $data = array_merge($data, $tmpData);
        }

        return $this->controller->render('kd-win', [
            'rules' => $data,
            'maps' => $this->maps,
            'weapons' => $this->weapons,
            'filter' => $filter,
        ]);
    }

    private function makeData(Rule $rule, BattleFilterForm $filter): array
    {
        $ret = [];
        foreach (range(0, static::KD_LIMIT) as $i) {
            $tmp = [];
            foreach (range(0, static::KD_LIMIT) as $j) {
                $tmp[] = [
                    'battle' => 0,
                    'win' => 0,
                ];
            }
            $ret[] = $tmp;
        }

        $maxBattle = 0;
        foreach ($this->query($rule, $filter) as $row) {
            $i = $row['kill'] > static::KD_LIMIT ? static::KD_LIMIT : (int)$row['kill'];
            $j = $row['death'] > static::KD_LIMIT ? static::KD_LIMIT : (int)$row['death'];
            $ret[$i][$j]['battle'] += $row['count'];
            $ret[$i][$j]['win'] += $row['win'];
        }

        return $ret;
    }

    private function query(Rule $rule, BattleFilterForm $filter): array
    {
        $t = StatWeaponKDWinRate::tableName();
        $query = (new Query())
            ->select([
                'kill' => "{{{$t}}}.[[kill]]",
                'death' => "{{{$t}}}.[[death]]",
                'count' => "SUM({{{$t}}}.[[battle_count]])",
                'win' => "SUM({{{$t}}}.[[win_count]])",
            ])
            ->from($t)
            ->andWhere(["{{{$t}}}.[[rule_id]]" => $rule->id])
            ->groupBy(["{{{$t}}}.[[kill]]", "{{{$t}}}.[[death]]"]);

        if (!$filter->hasErrors()) {
            if ($filter->map != '') {
                $query->innerJoin('map', "{{{$t}}}.[[map_id]] = {{map}}.[[id]]")
                    ->andWhere(['{{map}}.[[key]]' => $filter->map]);
            }
            if ($filter->weapon != '') {
                $query->innerJoin('weapon', "{{{$t}}}.[[weapon_id]] = {{weapon}}.[[id]]");
                switch (substr($filter->weapon, 0, 1)) {
                    default:
                        $query->andWhere(['{{weapon}}.[[key]]' => $filter->weapon]);
                        break;

                    case '@':
                        $query
                            ->innerJoin(
                                'weapon_type',
                                '{{weapon}}.[[type_id]] = {{weapon_type}}.[[id]]',
                            )
                            ->andWhere([
                                '{{weapon_type}}.[[key]]' => substr($filter->weapon, 1),
                            ]);
                        break;

                    case '+':
                        $query
                            ->innerJoin(
                                'subweapon',
                                '{{weapon}}.[[subweapon_id]] = {{subweapon}}.[[id]]',
                            )
                            ->andWhere([
                                '{{subweapon}}.[[key]]' => substr($filter->weapon, 1),
                            ]);
                        break;

                    case '*':
                        $query
                            ->innerJoin(
                                'special',
                                '{{weapon}}.[[special_id]] = {{special}}.[[id]]',
                            )
                            ->andWhere([
                                '{{special}}.[[key]]' => substr($filter->weapon, 1),
                            ]);
                        break;
                }
            }
        }
        return $query->all();
    }

    public function getMaps(): array
    {
        $ret = [];
        foreach (Map::find()->all() as $map) {
            $ret[$map->key] = Yii::t('app-map', $map->name);
        }
        asort($ret);
        return array_merge(
            ['' => Yii::t('app-map', 'Any Stage')],
            $ret,
        );
    }

    public function getWeapons(): array
    {
        $ret = [
            '' => Yii::t('app-weapon', 'Any Weapon'),
        ];
        foreach (WeaponType::find()->orderBy('id ASC')->all() as $type) {
            $ret[Yii::t('app-weapon', $type->name)] = array_merge(
                [
                    "@{$type->key}" => Yii::t('app-weapon', 'All of {0}', [
                        Yii::t('app-weapon', $type->name),
                    ]),
                ],
                (function () use ($type): array {
                    $list = [];
                    foreach ($type->weapons as $weapon) {
                        $list[$weapon->key] = Yii::t('app-weapon', $weapon->name);
                    }
                    asort($list);
                    return $list;
                })(),
            );
        }
        return $ret;
    }
}

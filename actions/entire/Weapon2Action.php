<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\entire;

use Yii;
use app\models\Map2;
use app\models\Rule2;
use app\models\StatWeapon2Result;
use app\models\Weapon2;
use app\models\Weapon2StageFilterForm;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction;

class Weapon2Action extends ViewAction
{
    public $weapon;
    public $rule;

    public function init()
    {
        parent::init();

        $request = Yii::$app->request;
        $this->weapon = Weapon2::findOne(['key' => $request->get('weapon')]);
        $this->rule = Rule2::findOne(['key' => $request->get('rule')]);
        if (!$this->weapon || !$this->rule) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
    }

    public function run()
    {
        $maps = Map2::getSortedMap(function (ActiveQuery $query): void {
            if ($this->rule->key !== 'nawabari') {
                $query->excludeMystery();
            }
        });

        $stageFilter = Yii::createObject(Weapon2StageFilterForm::class);
        $stageFilter->load($_GET) && $stageFilter->validate();

        $winRate = ArrayHelper::map(
            // {{{
            StatWeapon2Result::find()
                ->innerJoinWith('map', false)
                ->andWhere([
                    'stat_weapon2_result.weapon_id' => $this->weapon->id,
                    'stat_weapon2_result.rule_id' => $this->rule->id,
                ])
                ->applyFilter($stageFilter, ['result'])
                ->groupBy('stat_weapon2_result.map_id')
                ->select([
                    'map'       => 'MAX(map2.key)',
                    'battles'   => 'SUM(stat_weapon2_result.battles)',
                    'wins'      => 'SUM(stat_weapon2_result.wins)',
                ])
                ->asArray()
                ->all(),
            'map',
            fn (array $row): array => [
                    'win' => (int)$row['wins'],
                    'lose' => (int)$row['battles'] - (int)$row['wins'],
                ],
            // }}}
        );

        $kills = ArrayHelper::map(
            // {{{
            StatWeapon2Result::find()
                ->innerJoinWith('map', false)
                ->andWhere([
                    'stat_weapon2_result.weapon_id' => $this->weapon->id,
                    'stat_weapon2_result.rule_id' => $this->rule->id,
                ])
                ->applyFilter($stageFilter)
                ->groupBy(['stat_weapon2_result.map_id', 'stat_weapon2_result.kill'])
                ->select([
                    'map'       => 'MAX(map2.key)',
                    'kill'      => 'stat_weapon2_result.kill',
                    'battles'   => 'SUM(stat_weapon2_result.battles)',
                ])
                ->orderBy([
                  'map' => SORT_ASC,
                  'kill' => SORT_ASC,
                ])
                ->asArray()
                ->all(),
            'kill',
            fn ($row) => [
                    'times' => (int)$row['kill'],
                    'battles' => (int)$row['battles'],
                ],
            'map',
            // }}}
        );

        $deaths = ArrayHelper::map(
            // {{{
            StatWeapon2Result::find()
                ->innerJoinWith('map', false)
                ->andWhere([
                    'stat_weapon2_result.weapon_id' => $this->weapon->id,
                    'stat_weapon2_result.rule_id' => $this->rule->id,
                ])
                ->applyFilter($stageFilter)
                ->groupBy(['stat_weapon2_result.map_id', 'stat_weapon2_result.death'])
                ->select([
                    'map'       => 'MAX(map2.key)',
                    'death'     => 'stat_weapon2_result.death',
                    'battles'   => 'SUM(stat_weapon2_result.battles)',
                ])
                ->orderBy([
                  'map' => SORT_ASC,
                  'death' => SORT_ASC,
                ])
                ->asArray()
                ->all(),
            'death',
            fn ($row) => [
                    'times' => (int)$row['death'],
                    'battles' => (int)$row['battles'],
                ],
            'map',
            // }}}
        );

        $specials = ArrayHelper::map(
            // {{{
            StatWeapon2Result::find()
                ->innerJoinWith('map', false)
                ->andWhere([
                    'stat_weapon2_result.weapon_id' => $this->weapon->id,
                    'stat_weapon2_result.rule_id' => $this->rule->id,
                ])
                ->applyFilter($stageFilter)
                ->groupBy(['stat_weapon2_result.map_id', 'stat_weapon2_result.special'])
                ->select([
                    'map'       => 'MAX(map2.key)',
                    'special'   => 'stat_weapon2_result.special',
                    'battles'   => 'SUM(stat_weapon2_result.battles)',
                ])
                ->orderBy([
                  'map' => SORT_ASC,
                  'special' => SORT_ASC,
                ])
                ->asArray()
                ->all(),
            'special',
            fn ($row) => [
                    'times' => (int)$row['special'],
                    'battles' => (int)$row['battles'],
                ],
            'map',
            // }}}
        );

        $assists = ArrayHelper::map(
            // {{{
            StatWeapon2Result::find()
                ->innerJoinWith('map', false)
                ->andWhere([
                    'stat_weapon2_result.weapon_id' => $this->weapon->id,
                    'stat_weapon2_result.rule_id' => $this->rule->id,
                ])
                ->applyFilter($stageFilter)
                ->groupBy(['stat_weapon2_result.map_id', 'stat_weapon2_result.assist'])
                ->select([
                    'map'       => 'MAX(map2.key)',
                    'assist'    => 'stat_weapon2_result.assist',
                    'battles'   => 'SUM(stat_weapon2_result.battles)',
                ])
                ->orderBy([
                  'map' => SORT_ASC,
                  'assist' => SORT_ASC,
                ])
                ->asArray()
                ->all(),
            'assist',
            fn ($row) => [
                    'times' => (int)$row['assist'],
                    'battles' => (int)$row['battles'],
                ],
            'map',
            // }}}
        );

        return $this->controller->render('weapon2', [
            'stageFilter' => $stageFilter,
            'weapon'    => $this->weapon,
            'rule'      => $this->rule,
            'maps'      => $maps,
            'winRate'   => $winRate,
            'kills'     => $kills,
            'deaths'    => $deaths,
            'specials'  => $specials,
            'assists'   => $assists,
        ]);
    }
}

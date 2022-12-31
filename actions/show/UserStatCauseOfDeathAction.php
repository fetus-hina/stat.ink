<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\show;

use Yii;
use app\models\BattleFilterForm;
use app\models\CauseOfDeathGroupForm;
use app\models\DeathReason;
use app\models\User;
use app\models\Weapon;
use yii\db\Query;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;

class UserStatCauseOfDeathAction extends BaseAction
{
    use UserStatFilterTrait;

    public function run()
    {
        $request = Yii::$app->getRequest();
        $user = User::findOne(['screen_name' => $request->get('screen_name')]);
        if (!$user) {
            throw new NotFoundHttpException(Yii::t('app', 'Could not find user'));
        }

        $filter = new BattleFilterForm();
        $filter->load($_GET);
        $filter->screen_name = $user->screen_name;
        $filter->validate();

        $group = new CauseOfDeathGroupForm();
        $group->load($_GET);
        $group->validate();

        return $this->controller->render('user-stat-cause-of-death', [
            'user' => $user,
            'list' => $this->getList($user, $filter, $group),
            'filter' => $filter,
            'group' => $group,
        ]);
    }

    public function getList(
        User $user,
        BattleFilterForm $filter,
        CauseOfDeathGroupForm $group
    ): array {
        $query = (new Query())
            ->from('battle')
            ->innerJoin(
                'battle_death_reason',
                '{{battle}}.[[id]] = {{battle_death_reason}}.[[battle_id]]',
            )
            ->innerJoin(
                'death_reason',
                '{{battle_death_reason}}.[[reason_id]] = {{death_reason}}.[[id]]',
            )
            ->leftJoin('rule', '{{battle}}.[[rule_id]] = {{rule}}.[[id]]')
            ->leftJoin('game_mode', '{{rule}}.[[mode_id]] = {{game_mode}}.[[id]]')
            ->leftJoin('lobby', '{{battle}}.[[lobby_id]] = {{lobby}}.[[id]]')
            ->leftJoin('map', '{{battle}}.[[map_id]] = {{map}}.[[id]]')
            ->leftJoin('weapon', '{{battle}}.[[weapon_id]] = {{weapon}}.[[id]]')
            ->leftJoin('weapon_type', '{{weapon}}.[[type_id]] = {{weapon_type}}.[[id]]')
            ->leftJoin('subweapon', '{{weapon}}.[[subweapon_id]] = {{subweapon}}.[[id]]')
            ->leftJoin('special', '{{weapon}}.[[special_id]] = {{special}}.[[id]]')
            ->leftJoin('rank', '{{battle}}.[[rank_id]] = {{rank}}.[[id]]')
            ->leftJoin('rank_group', '{{rank}}.[[group_id]] = {{rank_group}}.[[id]]')
            ->andWhere(['{{battle}}.[[user_id]]' => $user->id]);
        if ($filter && !$filter->hasErrors()) {
            $this->filter($query, $filter);
        }

        switch ($group->hasErrors() ? null : $group->level) {
            default:
                return $this->getListAsIs($query);

            case 'canonical':
                return $this->getListCanonical($query);

            case 'main-weapon':
                return $this->getListMainWeapon($query);

            case 'type':
                return $this->getListType($query);
        }
    }

    protected function getListAsIs(Query $query): array
    {
        $query
            ->select([
                'reason_id' => '{{death_reason}}.[[id]]',
                'count' => 'SUM({{battle_death_reason}}.[[count]])',
            ])
            ->groupBy('{{death_reason}}.[[id]]');

        $list = $query->createCommand()->queryAll();

        // 必要な死因名の一覧を作る
        $deathReasons = [];
        array_map(
            function ($o) use (&$deathReasons) {
                $deathReasons[$o->id] = $o->getTranslatedName();
            },
            DeathReason::findAll(['id' => array_map(fn ($row) => $row['reason_id'], $list)]),
        );

        $ret = array_map(
            fn ($row) => (object)[
                    'name' => $deathReasons[$row['reason_id']] ?? '?',
                    'count' => (int)$row['count'],
                ],
            $list,
        );
        usort($ret, fn ($a, $b) => $b->count <=> $a->count ?: strcasecmp($a->name, $b->name));
        return $ret;
    }

    protected function getListCanonical(Query $query): array
    {
        return $this->getListMainWeaponImpl($query, 'canonical_id');
    }

    protected function getListMainWeapon(Query $query): array
    {
        return $this->getListMainWeaponImpl($query, 'main_group_id');
    }

    private function getListMainWeaponImpl(Query $query, $column): array
    {
        $query
            ->select([
                'reason_id'     => '{{death_reason}}.[[id]]',
                'canonical_id'  => 'MAX({{deadly_weapon}}.[[canonical_id]])',
                'main_group_id' => 'MAX({{deadly_weapon}}.[[main_group_id]])',
                'count'         => 'SUM({{battle_death_reason}}.[[count]])',
            ])
            ->leftJoin(
                '{{weapon}} {{deadly_weapon}}',
                '{{death_reason}}.[[weapon_id]] = {{deadly_weapon}}.[[id]]',
            )
            ->groupBy('{{death_reason}}.[[id]]');

        $list = $query->createCommand()->queryAll();

        // 必要な死因名の一覧を作る
        $deathReasons = [];
        $tmp = DeathReason::findAll(['id' => array_map(fn ($row) => $row['reason_id'], $list)]);
        foreach ($tmp as $o) {
            $deathReasons[$o->id] = $o->getTranslatedName();
        }

        // 必要なブキ名の一覧を作る
        $weapons = [];
        $tmp = Weapon::findAll(['id' => array_map(fn ($row) => $row[$column], $list)]);
        foreach ($tmp as $o) {
            if ($column === 'canonical_id') {
                $weapons[$o->id] = Yii::t('app-weapon', $o->name);
            } else {
                $weapons[$o->id] = Yii::t('app', '{0} etc.', Yii::t('app-weapon', $o->name));
            }
        }

        $retWeapons = [];
        $retOthers = [];
        foreach ($list as $row) {
            if ($row[$column] === null) {
                $retOthers[] = (object)[
                    'name' => $deathReasons[$row['reason_id']] ?? '?',
                    'count' => (int)$row['count'],
                ];
            } else {
                if (!isset($retWeapons[$row[$column]])) {
                    $retWeapons[$row[$column]] = (object)[
                        'name' => $weapons[$row[$column]] ?? '?',
                        'count' => (int)$row['count'],
                    ];
                } else {
                    $retWeapons[$row[$column]]->count += (int)$row['count'];
                }
            }
        }
        $ret = array_merge(
            array_values($retWeapons),
            array_values($retOthers),
        );
        usort($ret, fn ($a, $b) => $b->count <=> $a->count ?: strcasecmp($a->name, $b->name));
        return $ret;
    }

    protected function getListType(Query $query): array
    {
        $query
            ->select([
                'id'    => '{{death_reason_type}}.[[id]]',
                'name'  => 'MAX({{death_reason_type}}.[[name]])',
                'count' => 'SUM({{battle_death_reason}}.[[count]])',
            ])
            ->leftJoin('{{death_reason_type}}', '{{death_reason}}.[[type_id]] = {{death_reason_type}}.[[id]]')
            ->groupBy('{{death_reason_type}}.[[id]]');

        $ret = array_map(
            fn ($row) => (object)[
                    'name' => Yii::t('app-death', $row['id'] === null ? 'Unknown' : $row['name']),
                    'count' => (int)$row['count'],
                ],
            $query->createCommand()->queryAll(),
        );
        usort($ret, fn ($a, $b) => $b->count <=> $a->count ?: strcasecmp($a->name, $b->name));
        return $ret;
    }
}

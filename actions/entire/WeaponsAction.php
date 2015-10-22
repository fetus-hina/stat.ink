<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\entire;

use Yii;
use yii\web\ViewAction as BaseAction;
use app\models\Weapon;

class WeaponsAction extends BaseAction
{
    public function run()
    {
        return $this->controller->render('weapons.tpl', [
            'weapons' => $this->weaponStats,
        ]);
    }

    public function getWeaponStats()
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

    public function getWeapons(array $weaponIdList) {
        $list = Weapon::find()
            ->andWhere(['in', '{{weapon}}.[[id]]', $weaponIdList])
            ->all();
        $ret = [];
        foreach ($list as $weapon) {
            $ret[$weapon->id] = $weapon;
        }
        return $ret;
    }
}

<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\models\query;

use app\models\StatWeaponVsWeapon;
use app\models\Weapon;
use yii\db\ActiveQuery;

/**
 * @extends ActiveQuery<StatWeaponVsWeapon>
 */
final class StatWeaponVsWeaponQuery extends ActiveQuery
{
    public function weapon($weapon): self
    {
        return $this->weaponImpl(
            $weapon instanceof Weapon ? $weapon->id : (int)$weapon,
        );
    }

    private function weaponImpl(int $weaponId): self
    {
        return $this->andWhere(['or',
            [
                '{{stat_weapon_vs_weapon}}.[[weapon_id_1]]' => $weaponId,
                '{{stat_weapon_vs_weapon}}.[[weapon_id_2]]' => $weaponId,
            ],
        ]);
    }
}

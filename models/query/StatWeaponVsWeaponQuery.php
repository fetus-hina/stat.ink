<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\query;

use app\models\Weapon;
use yii\db\ActiveQuery;

final class StatWeaponVsWeaponQuery extends ActiveQuery
{
    /**
     * @param Weapon|int $weapon
     */
    public function weapon($weapon): self
    {
        return $this->weaponImpl(
            $weapon instanceof Weapon ? $weapon->id : (int)$weapon
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

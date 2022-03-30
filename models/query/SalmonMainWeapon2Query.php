<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\query;

use yii\db\ActiveQuery;

use const SORT_ASC;

final class SalmonMainWeapon2Query extends ActiveQuery
{
    public function sorted(): self
    {
        $kumaFirst = sprintf('(CASE %s END)', implode(' ', [
            'WHEN {{salmon_main_weapon2}}.[[weapon_id]] IS NULL THEN 0',
            'ELSE 1',
        ]));

        return $this
            ->joinWith([
                'weapon',
                'weapon.type',
            ])
            ->orderBy([
                $kumaFirst => SORT_ASC,
                '{{weapon_type2}}.[[category_id]]' => SORT_ASC,
                '{{weapon_type2}}.[[rank]]' => SORT_ASC,
                '{{salmon_main_weapon2}}.[[key]]' => SORT_ASC,
            ]);
    }
}

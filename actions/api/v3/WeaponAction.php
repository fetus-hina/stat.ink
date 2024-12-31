<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\v3;

use app\actions\api\v3\traits\ApiInitializerTrait;
use app\components\formatters\api\v3\WeaponApiFormatter;
use app\models\Weapon3;
use yii\web\ViewAction;

use function array_map;

use const SORT_ASC;

final class WeaponAction extends ViewAction
{
    use ApiInitializerTrait;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->apiInit();
    }

    /**
     * @return array[]
     */
    public function run(bool $full = false): array
    {
        return array_map(
            fn (Weapon3 $model): array => WeaponApiFormatter::toJson($model, $full),
            Weapon3::find()
                ->joinWith(['mainweapon.type'], true)
                ->with(['canonical', 'mainweapon', 'subweapon', 'weapon3Aliases'])
                ->orderBy([
                    '{{%weapon_type3}}.[[rank]]' => SORT_ASC,
                    '{{%weapon3}}.[[key]]' => SORT_ASC,
                ])
                ->all(),
        );
    }
}

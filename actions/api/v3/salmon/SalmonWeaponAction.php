<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\v3\salmon;

use app\actions\api\v3\traits\ApiInitializerTrait;
use app\components\formatters\api\v3\SalmonWeaponApiFormatter;
use app\models\SalmonWeapon3;
use yii\helpers\ArrayHelper;
use yii\web\ViewAction;

use function array_values;

use const SORT_ASC;

final class SalmonWeaponAction extends ViewAction
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
        return array_values(
            ArrayHelper::getColumn(
                SalmonWeapon3::find()
                    ->with(['salmonWeapon3Aliases'])
                    ->orderBy([
                        '(weapon_id IS NULL)' => SORT_ASC,
                        'key' => SORT_ASC,
                    ])
                    ->all(),
                fn (SalmonWeapon3 $model): array => SalmonWeaponApiFormatter::toJson($model, $full),
            ),
        );
    }
}
